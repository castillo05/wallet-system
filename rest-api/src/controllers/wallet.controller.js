const soap = require('soap');

// SOAP client configuration
const SOAP_URL = process.env.SOAP_URL || 'http://localhost:8000/soap?wsdl';
let soapClient = null;

// Initialize SOAP client
const initializeSoapClient = async () => {
  if (!soapClient) {
    try {
      soapClient = await soap.createClientAsync(SOAP_URL);
      console.log('SOAP Client Methods:', Object.keys(soapClient));
    } catch (error) {
      console.error('Error initializing SOAP client:', error);
      throw new Error('Failed to initialize SOAP client');
    }
  }
  return soapClient;
};

// Helper function to extract SOAP response
const extractSoapResponse = (soapResult) => {
  // Log the response structure to debug
  console.log('SOAP Response:', JSON.stringify(soapResult, null, 2));
  
  // The response might be nested in a 'return' or 'parameters' property
  const response = soapResult[0] || {};
  
  return {
    status: response.success === true ? 'success' : 'error',
    error: response.success === true ? null : {
      code: response.cod_error || 'UNKNOWN',
      message: response.message_error || 'Unknown error'
    },
    data: response.data
  };
};

// Controller methods
exports.createWallet = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { document, name, email, phone } = req.body;
    
    // Validate required fields according to WSDL
    if (!document || !name || !email || !phone) {
      return res.status(400).json({
        status: 'error',
        message: 'All fields are required: document, name, email, phone'
      });
    }

    const result = await client.registerClientAsync({
      document: String(document),
      name: String(name),
      email: String(email),
      phone: String(phone)
    });
    
    res.json(extractSoapResponse(result));
  } catch (error) {
    console.error('SOAP Error:', error);
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
};

exports.getWallet = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { id } = req.params;
    
    const result = await client.GetWallet({
      walletId: id
    });
    
    res.json({
      status: 'success',
      data: result
    });
  } catch (error) {
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
};

exports.deposit = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { document, amount } = req.body;
    
    if (!document || amount === undefined) {
      return res.status(400).json({
        status: 'error',
        message: 'Document and amount are required'
      });
    }

    const result = await client.loadBalanceAsync({
      document: String(document),
      amount: parseFloat(amount)
    });
    
    res.json(extractSoapResponse(result));
  } catch (error) {
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
};

exports.withdraw = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { walletId, amount } = req.body;
    
    const result = await client.Withdraw({
      walletId,
      amount
    });
    
    res.json({
      status: 'success',
      data: result
    });
  } catch (error) {
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
};

exports.getBalance = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { document } = req.params;
    
    if (!document) {
      return res.status(400).json({
        status: 'error',
        message: 'Document is required'
      });
    }

    const result = await client.getBalanceAsync({
      document: String(document)
    });
    
    res.json(extractSoapResponse(result));
  } catch (error) {
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
};

exports.getTransactions = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { id } = req.params;
    
    const result = await client.GetTransactions({
      walletId: id
    });
    
    res.json({
      status: 'success',
      data: result
    });
  } catch (error) {
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
};

exports.createPaymentSession = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { document, amount } = req.body;
    
    if (!document || amount === undefined) {
      return res.status(400).json({
        status: 'error',
        message: 'Document and amount are required'
      });
    }

    const result = await client.createPaymentSessionAsync({
      document: String(document),
      amount: parseFloat(amount)
    });
    
    res.json(extractSoapResponse(result));
  } catch (error) {
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
};

exports.confirmPayment = async (req, res) => {
  try {
    const client = await initializeSoapClient();
    const { token, amount } = req.body;
    
    if (!token || amount === undefined) {
      return res.status(400).json({
        status: 'error',
        message: 'Token and amount are required'
      });
    }

    const result = await client.confirmPaymentAsync({
      token: String(token),
      amount: parseFloat(amount)
    });
    
    res.json(extractSoapResponse(result));
  } catch (error) {
    res.status(500).json({
      status: 'error',
      message: error.message
    });
  }
}; 
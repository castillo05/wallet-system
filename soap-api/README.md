# Wallet System - SOAP API

A SOAP-based wallet system API built with Symfony that allows managing client wallets, processing payments, and handling transactions.

## Features

- Client Management
- Wallet Operations
- Payment Processing
- Transaction History
- Secure Payment Sessions

## Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Composer
- Symfony CLI (optional)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd soap-api
```

2. Install dependencies:
```bash
composer install
```

3. Configure your database in `.env`:
```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/soap_wallet?serverVersion=8.0"
```

4. Create database and run migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## Available SOAP Services

### 1. Register Client
Registers a new client and creates their wallet.

```xml
<soap:registerClientRequest>
    <document>12345678</document>
    <name>John Doe</name>
    <email>john@example.com</email>
    <phone>+1234567890</phone>
</soap:registerClientRequest>
```

### 2. Get Balance
Retrieves client's wallet balance.

```xml
<soap:getBalanceRequest>
    <document>12345678</document>
</soap:getBalanceRequest>
```

### 3. Load Balance
Adds funds to client's wallet.

```xml
<soap:loadBalanceRequest>
    <document>12345678</document>
    <amount>100.00</amount>
</soap:loadBalanceRequest>
```

### 4. Create Payment Session
Initiates a payment session.

```xml
<soap:createPaymentSessionRequest>
    <document>12345678</document>
    <amount>50.00</amount>
</soap:createPaymentSessionRequest>
```

### 5. Confirm Payment
Confirms and processes a payment.

```xml
<soap:confirmPaymentRequest>
    <token>generated-token</token>
    <amount>50.00</amount>
</soap:confirmPaymentRequest>
```

## Error Codes

- `00`: Success
- `01`: Missing required fields
- `02`: Client already exists
- `03`: Client not found
- `04`: Insufficient balance
- `05`: Invalid or used payment session
- `99`: Internal server error

## Testing the Services

You can test the SOAP services using any SOAP client (SoapUI, Postman, etc.) with these endpoints:

- WSDL URL: `http://localhost:8000/soap?wsdl`
- Service URL: `http://localhost:8000/soap`

Example using curl:
```bash
curl -X POST http://localhost:8000/soap \
  -H 'Content-Type: text/xml' \
  -d '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soap="http://localhost:8000/soap">
       <soapenv:Header/>
       <soapenv:Body>
          <soap:getBalanceRequest>
             <document>12345678</document>
          </soap:getBalanceRequest>
       </soapenv:Body>
    </soapenv:Envelope>'
```

## Development

### Running the Development Server

```bash
symfony server:start
# or
php -S localhost:8000 -t public/
```

### Creating New Migrations

After modifying entities:
```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Security Considerations

- All amounts are validated to prevent negative values
- Payment sessions use secure token generation
- Transactions are atomic and logged
- Client document uniqueness is enforced

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Contacto

JC Developer..!

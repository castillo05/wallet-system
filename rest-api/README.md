# Wallet System REST API

A REST API service that acts as a bridge between clients and a SOAP-based wallet system. This service provides endpoints for client registration, balance management, and payment processing.

## Features

- Client Registration
- Balance Management (check balance and deposits)
- Payment Processing (create sessions and confirm payments)
- SOAP Integration
- Input Validation
- Error Handling

## Prerequisites

- Node.js (v14 or higher)
- npm (v6 or higher)
- Running SOAP Wallet Service

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd wallet-system-rest-api
```

2. Install dependencies:
```bash
npm install
```

3. Configure environment variables:
```bash
cp .env.example .env
```
Then edit `.env` with your configuration:
```
PORT=3000
NODE_ENV=development
SOAP_URL=http://localhost:8000/soap?wsdl
```

## Running the Application

Development mode with auto-reload:
```bash
npm run dev
```

Production mode:
```bash
npm start
```

## API Endpoints

### Client Registration

Register a new client in the wallet system.

- **POST** `/api/wallet/register`
- **Body:**
```json
{
  "document": "12345678",
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "123456789"
}
```
- **Response:**
```json
{
  "status": "success",
  "data": {
    "clientId": "12345678"
  }
}
```

### Balance Management

#### Get Balance

Check the current balance of a wallet.

- **GET** `/api/wallet/balance/:document`
- **Response:**
```json
{
  "status": "success",
  "data": {
    "balance": 100.50
  }
}
```

#### Deposit (Load Balance)

Add funds to a wallet.

- **POST** `/api/wallet/deposit`
- **Body:**
```json
{
  "document": "12345678",
  "amount": 50.00
}
```
- **Response:**
```json
{
  "status": "success",
  "data": {
    "newBalance": 150.50
  }
}
```

### Payment Processing

#### Create Payment Session

Initialize a new payment session.

- **POST** `/api/wallet/payment/create`
- **Body:**
```json
{
  "document": "12345678",
  "amount": 25.00
}
```
- **Response:**
```json
{
  "status": "success",
  "data": {
    "token": "payment_session_token",
    "expiresAt": "2024-03-21T15:30:00Z"
  }
}
```

#### Confirm Payment

Confirm and process a payment.

- **POST** `/api/wallet/payment/confirm`
- **Body:**
```json
{
  "token": "payment_session_token",
  "amount": 25.00
}
```
- **Response:**
```json
{
  "status": "success",
  "data": {
    "transactionId": "tx_123456",
    "status": "completed"
  }
}
```

## Error Handling

The API returns appropriate HTTP status codes and error messages in JSON format:

```json
{
  "status": "error",
  "error": {
    "code": "ERROR_CODE",
    "message": "Error description"
  }
}
```

Common error codes:
- `400`: Bad Request - Invalid input
- `404`: Not Found - Resource not found
- `500`: Internal Server Error - Server-side error

## Development

### Project Structure

```
.
├── src/
│   ├── app.js              # Application entry point
│   ├── controllers/        # Request handlers
│   │   └── wallet.controller.js
│   └── routes/            # API routes
│       └── wallet.routes.js
├── package.json
└── README.md
```

### Commits Structure

The project follows atomic commits by functionality:

1. **SOAP Client Setup**
   - Basic SOAP client infrastructure
   - Response helper functions
   - Error handling setup

2. **Client Registration**
   - Registration endpoint
   - Input validation
   - SOAP integration for registration

3. **Balance Management**
   - Balance check endpoint
   - Deposit functionality
   - Balance-related validations

4. **Payment Processing**
   - Payment session creation
   - Payment confirmation
   - Payment flow validations

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes using atomic commits by functionality
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 
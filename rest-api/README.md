# Wallet System REST API

This is a REST API that consumes a SOAP wallet service. It provides endpoints for managing digital wallets, including creation, deposits, withdrawals, and balance checking.

## Prerequisites

- Node.js (v14 or higher)
- npm (v6 or higher)
- Running SOAP wallet service

## Installation

1. Clone the repository
2. Install dependencies:
```bash
npm install
```
3. Configure environment variables:
   - Copy `.env.example` to `.env`
   - Update the `SOAP_URL` to point to your SOAP service

## Running the API

Development mode:
```bash
npm run dev
```

Production mode:
```bash
npm start
```

## API Endpoints

### Create Wallet
- **POST** `/api/wallet/create`
- Body:
```json
{
  "userId": "string",
  "initialBalance": number
}
```

### Get Wallet
- **GET** `/api/wallet/:id`

### Deposit
- **POST** `/api/wallet/deposit`
- Body:
```json
{
  "walletId": "string",
  "amount": number
}
```

### Withdraw
- **POST** `/api/wallet/withdraw`
- Body:
```json
{
  "walletId": "string",
  "amount": number
}
```

### Get Balance
- **GET** `/api/wallet/balance/:id`

### Get Transactions
- **GET** `/api/wallet/transactions/:id`

## Error Handling

The API returns appropriate HTTP status codes and error messages in JSON format:

```json
{
  "status": "error",
  "message": "Error description"
}
```

## Development

To run tests:
```bash
npm test
``` 
# Wallet System

A comprehensive digital wallet system with both SOAP and REST APIs, designed to handle electronic transactions, user management, and wallet operations.

## Overview

This wallet system provides a dual-API approach to handle different types of integrations:

### SOAP API (soap-api/)

A Symfony-based SOAP service that provides:
- Client management (creation)
- Transaction processing
- Wallet operations
- Secure authentication
- Detailed transaction history

**Tech Stack:**
- PHP 8.1+
- Symfony Framework
- Doctrine ORM
- MySQL/MariaDB
- XML/SOAP

### REST API (rest-api/)

An Express.js-based REST service offering:
- Modern REST endpoints
- JSON-based communication
- Transaction processing
- User management

**Tech Stack:**
- Node.js
- Express.js
- REST/JSON

### Prerequisites

- PHP 8.1 or higher
- Node.js 18 or higher
- MySQL/MariaDB
- MongoDB
- Composer
- npm

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd wallet-system
```

2. Set up SOAP API:
```bash
cd soap-api
composer install
cp .env .env.local
# Configure your .env.local file
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

3. Set up REST API:
```bash
cd ../rest-api
npm install
cp .env.example .env
# Configure your .env file
```

## Features

- Dual API support (SOAP & REST)
- Transaction management
- Wallet operations (load/pay)
- Client management
- Transaction history
- Balance inquiries

## API Documentation

### SOAP API Endpoints

Available at: `http://your-domain/soap?wsdl`

Key operations:
- `createClient(ClientData)`
- `createTransaction(TransactionData)`
- `getBalance(ClientId)`
- `getTransactionHistory(ClientId)`

## Database

The system uses two databases:
- MySQL/MariaDB for SOAP API

Database schemas are available in the `db/` directory.


## Authors

JC Developer.

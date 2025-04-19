const express = require('express');
const router = express.Router();
const walletController = require('../controllers/wallet.controller');

// Client registration
router.post('/register', walletController.createWallet);

// Balance operations
router.get('/balance/:document', walletController.getBalance);
router.post('/deposit', walletController.deposit);

// Payment operations
router.post('/payment/create', walletController.createPaymentSession);
router.post('/payment/confirm', walletController.confirmPayment);

module.exports = router; 
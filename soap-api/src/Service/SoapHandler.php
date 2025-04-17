<?php
namespace App\Service;

use App\Entity\Client;
use App\Entity\Wallet;
use App\Entity\Transaction;
use App\Entity\PaymentSession;
use Doctrine\ORM\EntityManagerInterface;

class SoapHandler
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * @param object $parameters
     * @return array
     */
    public function registerClient($parameters): array
    {
        $document = $parameters->document ?? null;
        $name = $parameters->name ?? null;
        $email = $parameters->email ?? null;
        $phone = $parameters->phone ?? null;

        if (!$document || !$name || !$email || !$phone) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Todos los campos son obligatorios',
                'data' => null,
            ];
        }

        try {
            // Check if client already exists
            $existingClient = $this->em->getRepository(Client::class)->findOneBy(['document' => $document]);
            if ($existingClient) {
                return [
                    'success' => false,
                    'cod_error' => '02',
                    'message_error' => 'Ya existe un cliente con este documento',
                    'data' => null,
                ];
            }

            // Create new client
            $client = new Client();
            $client->setDocument($document);
            $client->setName($name);
            $client->setEmail($email);
            $client->setPhone($phone);

            // Create wallet for client
            $wallet = new Wallet();
            $wallet->setBalance(0.0);
            $wallet->setClient($client);
            
            // Persist both entities
            $this->em->persist($client);
            $this->em->persist($wallet);
            $this->em->flush();

            return [
                'success' => true,
                'cod_error' => '00',
                'message_error' => 'Cliente registrado exitosamente',
                'data' => [
                    'id' => $client->getId(),
                    'document' => $client->getDocument(),
                    'name' => $client->getName(),
                    'email' => $client->getEmail(),
                    'phone' => $client->getPhone(),
                    'wallet_balance' => $wallet->getBalance()
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'cod_error' => '99',
                'message_error' => 'Error interno del servidor: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * @param object $parameters
     * @return array
     */
    public function getBalance($parameters): array
    {
        $document = $parameters->document ?? null;

        if (!$document) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'El documento es obligatorio',
                'data' => null,
            ];
        }

        $client = $this->em->getRepository(Client::class)->findOneBy(['document' => $document]);
        
        if (!$client) {
            return [
                'success' => false,
                'cod_error' => '03',
                'message_error' => 'Cliente no encontrado',
                'data' => null,
            ];
        }

        $wallet = $this->em->getRepository(Wallet::class)->findOneBy(['client' => $client]);

        return [
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Consulta exitosa',
            'data' => [
                'balance' => $wallet->getBalance(),
                'client' => [
                    'name' => $client->getName(),
                    'document' => $client->getDocument(),
                ]
            ],
        ];
    }

    /**
     * @param object $parameters
     * @return array
     */
    public function loadBalance($parameters): array
    {
        $document = $parameters->document ?? null;
        $amount = $parameters->amount ?? null;

        if (!$document || !$amount || $amount <= 0) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Documento y monto mayor a 0 son obligatorios',
                'data' => null,
            ];
        }

        $client = $this->em->getRepository(Client::class)->findOneBy(['document' => $document]);
        
        if (!$client) {
            return [
                'success' => false,
                'cod_error' => '03',
                'message_error' => 'Cliente no encontrado',
                'data' => null,
            ];
        }

        $wallet = $this->em->getRepository(Wallet::class)->findOneBy(['client' => $client]);
        
        // Create transaction record
        $transaction = new Transaction();
        $transaction->setType('load');
        $transaction->setAmount((float)$amount);
        $transaction->setClient($client);
        
        // Update wallet balance
        $wallet->setBalance($wallet->getBalance() + (float)$amount);
        
        $this->em->persist($transaction);
        $this->em->flush();

        return [
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Recarga exitosa',
            'data' => [
                'new_balance' => $wallet->getBalance(),
                'transaction_id' => $transaction->getId(),
            ],
        ];
    }

    /**
     * @param object $parameters
     * @return array
     */
    public function createPaymentSession($parameters): array
    {
        $document = $parameters->document ?? null;
        $amount = $parameters->amount ?? null;

        if (!$document || !$amount || $amount <= 0) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Documento y monto mayor a 0 son obligatorios',
                'data' => null,
            ];
        }

        $client = $this->em->getRepository(Client::class)->findOneBy(['document' => $document]);
        
        if (!$client) {
            return [
                'success' => false,
                'cod_error' => '03',
                'message_error' => 'Cliente no encontrado',
                'data' => null,
            ];
        }

        $wallet = $this->em->getRepository(Wallet::class)->findOneBy(['client' => $client]);
        
        if ($wallet->getBalance() < $amount) {
            return [
                'success' => false,
                'cod_error' => '04',
                'message_error' => 'Saldo insuficiente',
                'data' => null,
            ];
        }

        // Create payment session
        $session = new PaymentSession();
        $session->setClient($client);
        $session->setToken($this->generateToken());
        
        $this->em->persist($session);
        $this->em->flush();

        return [
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Sesión de pago creada',
            'data' => [
                'token' => $session->getToken(),
                'amount' => (float)$amount,
            ],
        ];
    }

    /**
     * @param object $parameters
     * @return array
     */
    public function confirmPayment($parameters): array
    {
        $token = $parameters->token ?? null;
        $amount = $parameters->amount ?? null;

        if (!$token || !$amount || $amount <= 0) {
            return [
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Token y monto mayor a 0 son obligatorios',
                'data' => null,
            ];
        }

        $session = $this->em->getRepository(PaymentSession::class)->findOneBy([
            'token' => $token,
            'confirmed' => false
        ]);

        if (!$session) {
            return [
                'success' => false,
                'cod_error' => '05',
                'message_error' => 'Sesión de pago no válida o ya utilizada',
                'data' => null,
            ];
        }

        $client = $session->getClient();
        $wallet = $this->em->getRepository(Wallet::class)->findOneBy(['client' => $client]);

        if ($wallet->getBalance() < $amount) {
            return [
                'success' => false,
                'cod_error' => '04',
                'message_error' => 'Saldo insuficiente',
                'data' => null,
            ];
        }

        // Create transaction and update balance
        $transaction = new Transaction();
        $transaction->setType('pay');
        $transaction->setAmount((float)$amount);
        $transaction->setClient($client);
        
        $wallet->setBalance($wallet->getBalance() - (float)$amount);
        
        // Mark session as confirmed
        $session->setConfirmed(true);
        
        $this->em->persist($transaction);
        $this->em->flush();

        return [
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Pago confirmado exitosamente',
            'data' => [
                'transaction_id' => $transaction->getId(),
                'new_balance' => $wallet->getBalance(),
            ],
        ];
    }
}
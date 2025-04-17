<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SoapHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;

class SoapController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/soap', name: 'soap_entry')]
    public function index(SoapHandler $soapHandler): Response
    {
        if (isset($_GET['wsdl'])) {
            $wsdlPath = $this->getParameter('kernel.project_dir') . '/public/soap.wsdl';
            if (!file_exists($wsdlPath)) {
                $this->logger->error('WSDL file not found at: ' . $wsdlPath);
                return new Response('WSDL file not found', 404);
            }
            return new Response(
                file_get_contents($wsdlPath),
                200,
                ['Content-Type' => 'text/xml']
            );
        }

        try {
            $options = [
                'uri' => 'http://localhost:8000/soap',
                'cache_wsdl' => WSDL_CACHE_NONE
            ];

            if (!extension_loaded('soap')) {
                throw new \RuntimeException('SOAP extension is not loaded');
            }

            $server = new \SoapServer($this->getParameter('kernel.project_dir') . '/public/soap.wsdl', $options);
            $server->setObject($soapHandler);
            
            ob_start();
            $server->handle();
            $response = ob_get_clean();

            if (empty($response)) {
                throw new \RuntimeException('Empty SOAP response');
            }

            return new Response(
                $response,
                200,
                ['Content-Type' => 'text/xml; charset=utf-8']
            );
        } catch (\Exception $e) {
            $this->logger->error('SOAP Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            $errorResponse = sprintf(
                '<?xml version="1.0" encoding="UTF-8"?>
                <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
                    <SOAP-ENV:Body>
                        <SOAP-ENV:Fault>
                            <faultcode>SOAP-ENV:Server</faultcode>
                            <faultstring>%s</faultstring>
                        </SOAP-ENV:Fault>
                    </SOAP-ENV:Body>
                </SOAP-ENV:Envelope>',
                htmlspecialchars($e->getMessage())
            );

            return new Response(
                $errorResponse,
                500,
                ['Content-Type' => 'text/xml; charset=utf-8']
            );
        }
    }
} 
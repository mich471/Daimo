<?php


namespace Softtek\MonitorIntegration\Service;

use Softtek\MonitorIntegration\Helper\ConfigHelper;
use Softtek\MonitorIntegration\Model\Enum\MonitorInterfacesUrls;

class MonitorIntegrationService
{
    protected $zendClient;

    protected $configHelper;

    protected $logger;

    public function __construct(
        \Zend\Http\Client $zendClient,
        ConfigHelper $configHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->zendClient = $zendClient;
        $this->configHelper = $configHelper;
        $this->logger = $logger;
    }

    public function executeCreaRemision($payload)
    {
        $monitorUrl = $this->configHelper->getServiceUrl() . MonitorInterfacesUrls::N1;
        try
        {
            $this->zendClient->reset();
            $this->zendClient->setUri($monitorUrl);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_POST);
            $this->zendClient->setHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
            $this->zendClient->setRawBody(json_encode($payload));
            $this->zendClient->send();
            $response = $this->zendClient->getResponse();

            // $this->logger->info("Response " . json_encode($response));

            return $response;
        }
        catch (\Zend\Http\Exception\RuntimeException $runtimeException)
        {
            $this->logger->error("Error while executing N1 on Monitor " . $runtimeException->getMessage());
            $response = '{"error":"'. $runtimeException->getMessage() .'"}';
            return json_decode($response);
        }
    }


    public function executeRemCancelacion($payload)
    {
        $monitorUrl = $this->configHelper->getServiceUrl() . MonitorInterfacesUrls::N9;
        $response = [];
        $this->logger->info("Request " . json_encode($payload));
        try
        {
            $this->zendClient->reset();
            $this->zendClient->setUri($monitorUrl);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_POST);
            $this->zendClient->setHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
            $this->zendClient->setRawBody(json_encode($payload));
            $this->zendClient->send();
            $response = $this->zendClient->getResponse();
            $this->logger->info("Response " . json_encode($response->getBody()));

            return $response;
        }
        catch (\Zend\Http\Exception\RuntimeException $runtimeException)
        {
            $this->logger->error("Error while executing N9 on Monitor " . $runtimeException->getMessage());
            $response = '{"error":"'. $runtimeException->getMessage() .'"}';
            $this->logger->info("Response form error " . ($response));
            return json_decode($response);
        }
    }

    public function getStock($request) {

        $monitorUrl = $this->configHelper->getServiceUrl() . MonitorInterfacesUrls::STOCK;
        $this->logger->info("URL " . $monitorUrl);
        $this->logger->info("Stock Request " .  json_encode($request));
        try
        {
            $this->zendClient->reset();
            $this->zendClient->setUri($monitorUrl);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_POST);
            $this->zendClient->setHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]);
            $this->zendClient->setRawBody(json_encode($request));
            $this->zendClient->send();
            $response = $this->zendClient->getResponse();

            $this->logger->info("Response from monitor " . $response->getBody());

            return json_decode($response->getBody());
        }
        catch (\Zend\Http\Exception\RuntimeException $runtimeException)
        {
            $this->logger->error("Error while executing N1 on Monitor " . $runtimeException->getMessage());
            return null;
        }
    }
}

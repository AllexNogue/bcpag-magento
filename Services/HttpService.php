<?php

namespace Brasilcash\Gateway\Services;

use Brasilcash\Gateway\Http\Curl;
use Brasilcash\Gateway\Model\Ui\ConfigProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

abstract class HttpService
{
    protected $curl;
    protected $config;
    protected $logger;

    public function __construct(ScopeConfigInterface $config, Curl $curl, LoggerInterface $logger)
    {
        $this->curl = $curl;
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->addHeader('Authorization', 'Bearer ' . $config->getValue('payment/' . ConfigProvider::CODE . '/merchant_gateway_key'));
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->config = $config;
        $this->logger = $logger;
    }
}

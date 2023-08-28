<?php

namespace Brasilcash\Gateway\Services;

use Brasilcash\Gateway\Model\Ui\ConfigProvider;

class TransactionService extends HttpService
{
    public function hasOpenbanking(): bool
    {
        try {
            $this->curl->get($this->config->getValue('payment/' . ConfigProvider::CODE . '/api_uri') . '/v1/transactions/openbanking');
            $response = json_decode($this->curl->getBody());
            return isset($response->openbanking) ? $response->openbanking : false;
        } catch (\Throwable $th) {
            $this->logger->error('Unable to get openbanking.', [
                'error' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ]);
            return false;
        }
    }
}

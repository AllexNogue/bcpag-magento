<?php

namespace Brasilcash\Gateway\Services;

use Brasilcash\Gateway\Model\Ui\ConfigProvider;

class CustomerService extends HttpService
{
    public function find(array $query)
    {
        try {
            $this->curl->post($this->config->getValue('payment/' . ConfigProvider::CODE . '/api_uri') . '/v1/customers/find', json_encode($query));
            $response = json_decode($this->curl->getBody());
            return $response;
        } catch (\Throwable $th) {
            $this->logger->error('Unable to find customer.', [
                'error' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ]);
            return null;
        }
    }

    public function getCustomerCards(string $email): array
    {
        try {
            $this->curl->post($this->config->getValue('payment/' . ConfigProvider::CODE . '/api_uri') . '/v1/customers/cards', json_encode(['email' => $email]));
            $response = json_decode($this->curl->getBody());
            return $response;
        } catch (\Throwable $th) {
            $this->logger->error('Unable to get customer cards.', [
                'error' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ]);
            return [];
        }
    }
}

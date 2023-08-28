<?php

namespace Brasilcash\Gateway\Services;

use Brasilcash\Gateway\Model\Ui\ConfigProvider;

class CardService extends HttpService
{
    public function delete(int $id)
    {
        try {
            $this->curl->delete($this->config->getValue('payment/' . ConfigProvider::CODE . '/api_uri') . '/v1/cards/' . $id);
            $response = json_decode($this->curl->getBody());
            return $response;
        } catch (\Throwable $th) {
            $this->logger->error('Unable to delete card.', [
                'error' => $th->getMessage(),
                'trace' => $th->getTrace(),
            ]);
            return null;
        }
    }
}

<?php

namespace Brasilcash\Gateway\Http;

class Curl extends \Magento\Framework\HTTP\Client\Curl
{
    public function delete($uri)
    {
        $this->makeRequest("DELETE", $uri);
    }
}

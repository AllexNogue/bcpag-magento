<?php

namespace Brasilcash\Gateway\Controller\Cards;

use Brasilcash\Gateway\Services\CardService;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpDeleteActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Remove implements HttpDeleteActionInterface
{
    protected $jsonFactory;
    protected $request;
    protected $cardService;

    public function __construct(
        JsonFactory $jsonFactory,
        RequestInterface $request,
        CardService $cardService
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->cardService = $cardService;
    }

    public function execute()
    {
        $this->cardService->delete($this->request->getParam('id'));
        return $this->jsonFactory->create()->setData([]);
    }
}

<?php

namespace Brasilcash\Gateway\Controller\Cards;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;

class Index implements HttpGetActionInterface
{
    protected $pageFactory;
    protected $request;

    public function __construct(PageFactory $pageFactory, RequestInterface $request)
    {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
    }

    public function execute()
    {
        return $this->pageFactory->create();
    }
}

<?php

namespace Brasilcash\Gateway\Block;

use Brasilcash\Gateway\Services\CustomerService;

class Cards extends \Magento\Framework\View\Element\Template
{
    protected $session;
    protected $customerService;
    public $cards = [];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        CustomerService $customerService,
        array $data = [],
    ) {
        parent::__construct($context, $data);
        $this->session = $session;
        $this->customerService = $customerService;
        $this->cards = $this->customerService->getCustomerCards($session->getCustomer()->getData('email'));
    }
}

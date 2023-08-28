<?php

namespace Brasilcash\Gateway\Model\Ui;

use Magento\Payment\Model\CcConfigProvider;

class ConfigProvider extends CcConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    /**
     * Payment Gateway Code
     */
    const CODE = 'brasilcashPaymentGateway';

    protected $customerService;
    protected $transactionService;
    protected $session;

    public function __construct(
        \Magento\Payment\Model\CcConfig $ccConfig,
        \Magento\Framework\View\Asset\Source $assetSource,
        \Brasilcash\Gateway\Services\CustomerService $customerService,
        \Brasilcash\Gateway\Services\TransactionService $transactionService,
        \Magento\Customer\Model\Session $session,
    ) {
        parent::__construct($ccConfig, $assetSource);
        $this->customerService = $customerService;
        $this->transactionService = $transactionService;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return [
            'payment' => [
                'ccform' => [
                    'availableTypes' => [self::CODE => $this->ccConfig->getCcAvailableTypes()],
                    'months' => [self::CODE => $this->ccConfig->getCcMonths()],
                    'years' => [self::CODE => $this->ccConfig->getCcYears()],
                    'hasVerification' => [self::CODE => $this->ccConfig->hasVerification()],
                    'cvvImageUrl' => [self::CODE => $this->ccConfig->getCvvImageUrl()]
                ],
                'cards' => $this->session->isLoggedIn()
                    ? $this->customerService->getCustomerCards($this->session->getCustomer()->getEmail())
                    : [],
                'openbanking' => $this->transactionService->hasOpenbanking()
            ]
        ];
    }
}

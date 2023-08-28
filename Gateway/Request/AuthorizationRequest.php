<?php

namespace Brasilcash\Gateway\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class AuthorizationRequest implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    private $bodyBuilder;

    /**
     * AuthorizationRequest constructor.
     * @param ConfigInterface $config
     */
    public function __construct(BodyBuilder $bodyBuilder)
    {
        $this->bodyBuilder = $bodyBuilder;
    }

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $paymentDO = $buildSubject['payment'];

        return $this->bodyBuilder->buildForCreateTransaction($paymentDO);
    }
}

<?php

namespace Brasilcash\Gateway\Gateway\Request;


use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class VoidRequest implements \Magento\Payment\Gateway\Request\BuilderInterface
{
    protected $bodyBuilder;

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
        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }

        return $this->bodyBuilder->buildForRefund($paymentDO);
    }
}

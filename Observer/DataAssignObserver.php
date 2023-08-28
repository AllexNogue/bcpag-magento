<?php

namespace Brasilcash\Gateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends \Magento\Payment\Observer\AbstractDataAssignObserver
{

    protected $additionalInformationList = [
        'cc_number',
        'cc_exp_month',
        'cc_exp_year',
        'cc_cid',
        'cpf',
        'card_id',
        'payment_method',
    ];

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}

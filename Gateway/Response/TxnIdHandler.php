<?php

namespace Brasilcash\Gateway\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class TxnIdHandler implements \Magento\Payment\Gateway\Response\HandlerInterface
{
    const TXN_ID = 'id'; // Transaction ID

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($handlingSubject['payment']) || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var \Magento\Payment\Gateway\Data\PaymentDataObject */
        $paymentDO = $handlingSubject['payment'];
        /** @var \Magento\Sales\Model\Order\Payment */
        $payment = $paymentDO->getPayment();

        $payment->setTransactionId($response[self::TXN_ID]);

        $removeInfo = ['cc_number', 'cc_exp_month', 'cc_exp_year', 'cc_cid', 'card_id', 'cpf'];
        foreach ($removeInfo as $key) {
            $payment->unsAdditionalInformation($key);
        }

        if (isset($response['pix_qr_code'])) {
            $payment->setAdditionalInformation('pix', [
                'pix_qr_code' => $response['pix_qr_code'],
                'pix_expiration_date' => $response['pix_expiration_date'],
                'pix_additional_fields' => $response['pix_additional_fields']
            ]);
        }

        $payment->setIsTransactionClosed(false);
    }
}

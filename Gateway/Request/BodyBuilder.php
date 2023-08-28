<?php

namespace Brasilcash\Gateway\Gateway\Request;

use Brasilcash\Gateway\Model\Ui\ConfigProvider;
use Brasilcash\Gateway\Services\CustomerService;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Data\Order\AddressAdapter;
use Magento\Payment\Gateway\Data\PaymentDataObject;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class BodyBuilder
{
    private $config;
    private $customerService;

    public function __construct(ScopeConfigInterface $config, CustomerService $customerService)
    {
        $this->config = $config;
        $this->customerService = $customerService;
    }

    public function buildForCreateTransaction(PaymentDataObject $paymentDO, $capture = false): array
    {
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        $shipping = $order->getShippingAddress();
        $billing = $order->getBillingAddress();

        $billingFullName = $this->getFullName($billing);
        $shippingFullName = $this->getFullName($shipping);

        $data = [
            '_extra' => [
                'merchant_key' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/merchant_gateway_key'),
                'uri' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/api_uri') . '/v1/transactions',
            ],
            'amount' => $order->getGrandTotalAmount() * 100,
            'payment_method' => $payment->getAdditionalInformation('payment_method'),
            'async' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/async'),
            'capture' => $capture,
            'billing' => [
                'name' => $billingFullName,
                'address' => [
                    'country' => $billing->getCountryId(),
                    'street' => $billing->getStreetLine1(),
                    'street_number' => $this->getNumberFromAddress($billing),
                    'state' => $billing->getRegionCode(),
                    'city' => $billing->getCity(),
                    'neighborhood' => $billing->getStreetLine2(),
                    'zipcode' => $billing->getPostcode(),
                ],
            ],
            'customer' => [
                'name' => $billingFullName,
                'email' => $billing->getEmail(),
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                    [
                        'type' => 'cpf',
                        'number' => $payment->getAdditionalInformation('cpf')
                    ]
                ],
                'external_id' => ($order->getCustomerId() ?? 0)
            ],
            'shipping' => [
                'name' => $shippingFullName,
                'address' => [
                    'country' => $shipping->getCountryId(),
                    'street' => $shipping->getStreetLine1(),
                    'street_number' => $this->getNumberFromAddress($shipping),
                    'state' => $shipping->getRegionCode(),
                    'city' => $shipping->getCity(),
                    'neighborhood' => $shipping->getStreetLine2(),
                    'zipcode' => $shipping->getPostcode(),
                ]
            ],
        ];

        if ($data['payment_method'] === 'credit_card') {
            $data['installments'] = 1; // TODO,
            if ($payment->getAdditionalInformation('card_id')) {
                $data['card_id'] = $payment->getAdditionalInformation('card_id');
            } else {
                $data['card'] = [
                    'card_number' => $payment->getAdditionalInformation('cc_number'),
                    'card_expiration_date' => $this->getFormattedCardExpiration($payment->getAdditionalInformation('cc_exp_month'), $payment->getAdditionalInformation('cc_exp_year')),
                    'card_holder_name' => $billingFullName,
                    'card_cvv' => $payment->getAdditionalInformation('cc_cid'),
                ];
            }
        }

        $customer = $this->customerService->find(['email' => $billing->getEmail()]);
        if ($customer && isset($customer->id)) {
            $data['customer_id'] = $customer->id;
        }

        return $data;
    }

    public function buildForCapture(PaymentDataObject $paymentDO): array
    {
        /** @var OrderPaymentInterface */
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        return [
            '_extra' => [
                'merchant_key' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/merchant_gateway_key'),
                'uri' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/api_uri') . '/v1/transactions/' . $payment->getLastTransId() . '/capture',
            ],
            'async' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/async'),
            'amount' => $order->getGrandTotalAmount() * 100
        ];
    }

    public function buildForRefund(PaymentDataObject $paymentDO): array
    {
        /** @var OrderPaymentInterface */
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        return [
            '_extra' => [
                'merchant_key' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/merchant_gateway_key'),
                'uri' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/api_uri') . '/v1/transactions/' . $payment->getLastTransId() . '/refund',
            ],
            'async' => $this->config->getValue('payment/' . ConfigProvider::CODE . '/async'),
            'amount' => $order->getGrandTotalAmount() * 100
        ];
    }

    protected function getFormattedCardExpiration($month, $year)
    {
        if (!$month || !$year) return '';
        return str_pad($month, 2, '0', STR_PAD_LEFT) . substr($year, -2);
    }

    protected function getFullName(AddressAdapter $address)
    {
        return $address->getFirstname() . ' ' . $address->getLastname();
    }

    protected function getNumberFromAddress(AddressAdapter $address)
    {
        preg_match('/\d+/', $address->getStreetLine1(), $matches);
        return $matches[1] ?? '';
    }
}

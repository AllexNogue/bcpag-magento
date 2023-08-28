<?php

namespace Brasilcash\Gateway\Block;

use Brasilcash\Gateway\Setup\Patch\Data\InstallStatus;
use Magento\Framework\UrlInterface;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\View\Element\Template;
use Magento\Sales\Model\OrderRepository;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class Pix extends Template
{
    public $pix;
    public $qrcode;

    /**
     * @inheritdoc
     */
    public function __construct(
        Context $context,
        UrlInterface $urlInterface,
        OrderRepository $orderRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $currentUrl = $urlInterface->getCurrentUrl();
        preg_match('/order_id\/(\d+)/', $currentUrl, $matches);
        $orderId = ((int) $matches[1]) ?? null;

        $order = $orderRepository->get($orderId);
        $additionalInformation = $order->getPayment()->getAdditionalInformation();
        if (isset($additionalInformation['pix']) && $order->getStatus() === InstallStatus::STATUS_WAITING_PAYMENT) {
            $this->pix = $additionalInformation['pix'];
            $this->qrcode = new Qrcode(new QROptions([
                'eccLevel' => QRCode::ECC_L,
                'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                'version' => QRCode::VERSION_AUTO,
            ]));
        }
    }
}

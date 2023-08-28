<?php

namespace Brasilcash\Gateway\Services;

use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Sales\Model\Order\Status\HistoryRepository;
use Magento\Sales\Model\OrderRepository;

class OrderStatusService
{
    protected const PREFIX = 'bc_';

    protected $transactionSearch;
    protected $orderRepository;
    protected $historyFactory;
    protected $historyRepository;

    public function __construct(
        OrderRepository $orderRepository,
        HistoryFactory $historyFactory,
        HistoryRepository $historyRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->historyFactory = $historyFactory;
        $this->historyRepository = $historyRepository;
    }

    public function updateOrderStatus($orderId, $status)
    {
        $status = self::PREFIX . $status;
        $order = $this->orderRepository->get($orderId);
        $order->setState($status)->setStatus($status);
        $this->orderRepository->save($order);

        $history = $this->historyFactory->create()
            ->setParentId($orderId)
            ->setComment(__('Status updated to %1.', $status))
            ->setEntityName('order')
            ->setStatus($status);
        $this->historyRepository->save($history);
    }
}

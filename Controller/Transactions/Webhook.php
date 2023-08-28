<?php

namespace Brasilcash\Gateway\Controller\Transactions;

use Brasilcash\Gateway\Services\OrderStatusService;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory;

class Webhook implements HttpPostActionInterface
{
    protected $jsonFactory;
    protected $request;
    /** @var \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection\Interceptor */
    protected $transactionSearch;
    protected $orderStatusService;

    public function __construct(
        JsonFactory $jsonFactory,
        RequestInterface $request,
        TransactionSearchResultInterfaceFactory $transactionSearch,
        OrderStatusService $orderStatusService
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->request = $request;
        $this->transactionSearch = $transactionSearch->create();
        $this->orderStatusService = $orderStatusService;
    }

    public function execute()
    {
        $body = $this->request->getParam('body');
        if (!isset($body->payload)) return;
        $payload = $body->payload;
        if (!isset($payload->status)) return;

        /**
         * @var int $key
         * @var \Magento\Sales\Model\Order\Payment\Transaction $transaction
         */
        foreach ($this->findTransactions($payload->id) as $key => $transaction) {
            $this->orderStatusService->updateOrderStatus($transaction->getOrderId(), $payload->status);
        }
        return $this->jsonFactory->create()->setData([]);
    }

    protected function findTransactions($transactionId)
    {
        return $this->transactionSearch
            ->addFilter('txn_id', $transactionId)
            ->getItems();
    }
}

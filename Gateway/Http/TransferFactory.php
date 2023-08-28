<?php

namespace Brasilcash\Gateway\Gateway\Http;

use Magento\Payment\Gateway\Http\TransferBuilder;

class TransferFactory implements \Magento\Payment\Gateway\Http\TransferFactoryInterface
{
    /** @var TransferBuilder */
    private $transferBuilder;

    /**
     * TransferFactory constructor.
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(TransferBuilder $transferBuilder)
    {
        $this->transferBuilder = $transferBuilder;
    }

    /**
     * @inheritDoc
     */
    public function create(array $request)
    {
        $_extra = $request['_extra'];
        unset($request['_extra']);

        return $this->transferBuilder
            ->setBody($request)
            ->setMethod($_extra['method'] ?? 'POST')
            ->setHeaders(
                [
                    'Authorization' => 'Bearer ' . $_extra['merchant_key'],
                    'User-Agent' => 'Brasilcash-Magent-Module',
                    'Content-Type' => 'application/json',
                ]
            )
            ->setUri($_extra['uri'])
            ->build();
    }
}

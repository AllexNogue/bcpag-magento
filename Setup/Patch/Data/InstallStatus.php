<?php

namespace Brasilcash\Gateway\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class InstallStatus implements DataPatchInterface, PatchRevertableInterface
{
    protected ModuleDataSetupInterface $moduleDataSetup;

    public const STATUS_PROCESSING  = 'bc_processing';
    public const STATUS_AUTHORIZED  = 'bc_authorized';
    public const STATUS_PAID  = 'bc_paid';
    public const STATUS_REFUNDED  = 'bc_refunded';
    public const STATUS_REFUSED  = 'bc_refused';
    public const STATUS_WAITING_PAYMENT  = 'bc_waiting_payment';

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $statuses = [
            self::STATUS_PROCESSING => __('Processing'),
            self::STATUS_AUTHORIZED => __('Authorized'),
            self::STATUS_PAID => __('Paid'),
            self::STATUS_REFUNDED => __('Refunded'),
            self::STATUS_REFUSED => __('Refused'),
            self::STATUS_WAITING_PAYMENT => __('Waiting Payment'),
        ];
        $data = $states = [];
        foreach ($statuses as $code => $info) {
            $data[]   = ['status' => $code, 'label' => $info];
            $states[] = [
                'status'           => $code,
                'state'            => $code,
                'is_default'       => 0,
                'visible_on_front' => 1,
            ];
        }
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sales_order_status'),
            ['status', 'label'],
            $data
        );
        $this->moduleDataSetup->getConnection()->insertMultiple(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $states
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function revert(): void
    {
        $adapter = $this->moduleDataSetup->getConnection();
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status'),
            $adapter->quoteInto('status = ?', self::STATUS_PROCESSING)
        );
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status'),
            $adapter->quoteInto('status = ?', self::STATUS_AUTHORIZED)
        );
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $adapter->quoteInto('status = ?', self::STATUS_PAID)
        );
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status'),
            $adapter->quoteInto('status = ?', self::STATUS_REFUNDED)
        );
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $adapter->quoteInto('status = ?', self::STATUS_REFUSED)
        );
        $adapter->delete(
            $this->moduleDataSetup->getTable('sales_order_status_state'),
            $adapter->quoteInto('status = ?', self::STATUS_WAITING_PAYMENT)
        );
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }
}

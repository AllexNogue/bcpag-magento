<?php

namespace Brasilcash\Gateway\Block;

use Magento\Framework\Phrase;

class Info extends \Magento\Payment\Block\ConfigurableInfo
{
    /**
     * Created By: Ashish Ranade
     * getLabel
     * @param string $field
     * @return Phrase|string
     */
    protected function getLabel($field)
    {
        return __($field);
    }

    /**
     * Created By: Ashish Ranade
     * getValueView
     * @param string $field
     * @param string $value
     * @return Phrase|string
     */
    protected function getValueView($field, $value)
    {
        return parent::getValueView($field, $value);
    }
}

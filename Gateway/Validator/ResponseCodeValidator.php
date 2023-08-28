<?php

namespace Brasilcash\Gateway\Gateway\Validator;

class ResponseCodeValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator
{
    /**
     * @inheritDoc
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            throw new \InvalidArgumentException('Response does not exist');
        }
        $response = $validationSubject['response'];
        if ($this->isSuccessfulTransaction($response)) {
            return $this->createResult(true);
        } else {
            $fails = [];
            if (isset($response['errors'])) {
                $fails = $response['errors'];
            } else if (isset($response['refused_reason'])) {
                $fails = [$response['refused_reason']->reason];
            } else {
                $fails = [__('Gateway rejected the transaction.')];
            }
            return $this->createResult(
                false,
                $fails
            );
        }
    }

    /**
     * @param array $response
     * @return bool
     */
    private function isSuccessfulTransaction(array $response)
    {
        if (isset($response['id']) && !empty($response['id'])) {
            return $response['status'] !== 'refused';
        }
        return (!isset($response['errors']) || sizeof($response['errors']) === 0)
            && $response['code'] >= 200
            && $response['code'] < 300;
    }
}

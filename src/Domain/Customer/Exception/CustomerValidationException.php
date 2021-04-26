<?php

namespace ChessableBanking\Domain\Customer\Exception;

use ChessableBanking\Domain\Customer\Validator\CustomerValidationError;
use Exception;

class CustomerValidationException extends Exception
{
    /**
     * @var CustomerValidationError[]
     */
    private array $errors = [];

    /**
     * @param CustomerValidationError[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('Customer is invalid');
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    private function addError(CustomerValidationError $customerValidationError): void
    {
        $this->errors[] = $customerValidationError;
    }

    /**
     * @return CustomerValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

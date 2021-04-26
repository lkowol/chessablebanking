<?php

namespace ChessableBanking\Domain\Customer\Exception;

use ChessableBanking\Domain\Customer\Validator\TransferValidationError;
use Exception;

class TransferValidationException extends Exception
{

    /**
     * @var TransferValidationError[]
     */
    private array $errors = [];

    /**
     * @param TransferValidationError[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('Transfer is not possible');
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    private function addError(TransferValidationError $transferValidationError): void
    {
        $this->errors[] = $transferValidationError;
    }

    /**
     * @return TransferValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

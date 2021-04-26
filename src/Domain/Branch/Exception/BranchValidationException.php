<?php

namespace ChessableBanking\Domain\Branch\Exception;

use ChessableBanking\Domain\Branch\Validator\BranchValidationError;
use Exception;

class BranchValidationException extends Exception
{

    /**
     * @var BranchValidationError[]
     */
    private array $errors = [];

    /**
     * @param BranchValidationError[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('Branch is invalid');
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    private function addError(BranchValidationError $branchValidationError): void
    {
        $this->errors[] = $branchValidationError;
    }

    /**
     * @return BranchValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

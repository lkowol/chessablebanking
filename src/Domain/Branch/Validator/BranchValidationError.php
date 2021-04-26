<?php

namespace ChessableBanking\Domain\Branch\Validator;

class BranchValidationError
{

    private string $fieldName;
    private string $errorMessage;

    public function __construct(string $fieldName, string $errorMessage)
    {
        $this->fieldName = $fieldName;
        $this->errorMessage = $errorMessage;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}

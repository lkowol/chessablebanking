<?php


namespace ChessableBanking\Domain\Location\Validator;

/*
 * Redundancy due to possibility of potential business differences between particular domain objects
 * Abstraction would unnecessarily bind the logic
 */
class LocationValidationError
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

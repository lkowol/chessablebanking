<?php

namespace ChessableBanking\Domain\Location\Exception;

use ChessableBanking\Domain\Location\Validator\LocationValidationError;
use Exception;

class LocationValidationException extends Exception
{
    /**
     * @var LocationValidationError[]
     */
    private array $errors = [];

    /**
     * @param LocationValidationError[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct('Location is invalid');
        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    private function addError(LocationValidationError $locationValidationError): void
    {
        $this->errors[] = $locationValidationError;
    }

    /**
     * @return LocationValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

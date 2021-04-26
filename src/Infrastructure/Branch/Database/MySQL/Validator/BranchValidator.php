<?php

namespace ChessableBanking\Infrastructure\Branch\Database\MySQL\Validator;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Exception\BranchValidationException;
use ChessableBanking\Domain\Branch\Validator\BranchValidationError;

class BranchValidator
{

    /**
     * @param Branch $branch
     * @throws BranchValidationException
     */
    public function validate(Branch $branch): void
    {
        $errors = [];

        if (strlen($branch->getName()) > 255) {
            $errors[] = new BranchValidationError('name', 'Name must not be longer than 255 characters');
        }

        if (!empty($errors)) {
            throw new BranchValidationException($errors);
        }
    }
}

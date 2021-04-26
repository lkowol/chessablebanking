<?php

namespace ChessableBanking\Infrastructure\Customer\Database\MySQL\Validator;

use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\CustomerValidationException;
use ChessableBanking\Domain\Customer\Validator\CustomerValidationError;

class CustomerValidator
{

    /**
     * @throws CustomerValidationException
     */
    public function validate(Customer $customer): void
    {
        $errors = [];

        $nameLength = strlen($customer->getName());
        if ($nameLength > 255) {
            $errors[] = new CustomerValidationError('name', 'Name must not be longer than 255 characters');
        }

        if (!empty($errors)) {
            throw new CustomerValidationException($errors);
        }
    }
}

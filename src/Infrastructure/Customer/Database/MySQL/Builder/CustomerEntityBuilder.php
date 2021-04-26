<?php

namespace ChessableBanking\Infrastructure\Customer\Database\MySQL\Builder;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Model\Balance;
use stdClass;

class CustomerEntityBuilder
{

    public function build(stdClass $customer, Branch $branch, Currency $currency): Customer
    {
        return new Customer(
            $customer->id ?? '',
            $customer->name ?? '',
            $branch,
            new Balance($customer->balance_amount, $currency)
        );
    }
}

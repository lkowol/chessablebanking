<?php


namespace ChessableBanking\UserInterface\Panel\Action\Customer\Builder;

use ChessableBanking\Application\Customer\Model\Customer;
use ChessableBanking\Domain\Branch\Entity\Branch;

class CustomerModelBuilder
{

    public function build(array $formData, Branch $branch): Customer
    {
        return new Customer(
            $formData['name'] ?? '',
            $formData['balanceAmount'] ?? 0.0,
            $formData['balanceCurrency'],
            $branch
        );
    }
}

<?php

namespace ChessableBanking\Domain\Customer\Repository;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\CustomerValidationException;

interface CustomerRepositoryInterface
{

    /**
     * @return Customer[]
     */
    public function findByBranch(Branch $branch): array;

    public function find(string $id): ?Customer;

    /**
     * @param Customer $customer
     * @return Customer
     * @throws CustomerValidationException
     */
    public function create(Customer $customer): Customer;

    public function transfer(Customer $sender, Customer $receiver, float $senderAmount, float $receiverAmount): void;
}

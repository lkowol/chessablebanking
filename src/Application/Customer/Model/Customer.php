<?php

namespace ChessableBanking\Application\Customer\Model;

use ChessableBanking\Domain\Branch\Entity\Branch;

class Customer
{

    private string $name;
    private float $balanceAmount;
    private string $balanceCurrencyId;
    private Branch $branch;

    public function __construct(string $name, float $balanceAmount, string $balanceCurrencyId, Branch $branch)
    {
        $this->name = $name;
        $this->balanceAmount = $balanceAmount;
        $this->balanceCurrencyId = $balanceCurrencyId;
        $this->branch = $branch;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBalanceAmount(): float
    {
        return $this->balanceAmount;
    }

    public function getBalanceCurrencyId(): string
    {
        return $this->balanceCurrencyId;
    }

    public function getBranch(): Branch
    {
        return $this->branch;
    }
}

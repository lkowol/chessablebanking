<?php

namespace ChessableBanking\Application\Report\Model;

class BranchWithBalance
{

    private string $name;
    private float $balanceAmount;
    private string $balanceCurrency;

    public function __construct(string $name, float $balanceAmount, string $balanceCurrency)
    {
        $this->name = $name;
        $this->balanceAmount = $balanceAmount;
        $this->balanceCurrency = $balanceCurrency;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBalanceAmount(): float
    {
        return $this->balanceAmount;
    }

    public function getBalanceCurrency(): string
    {
        return $this->balanceCurrency;
    }
}

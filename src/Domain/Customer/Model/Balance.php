<?php

namespace ChessableBanking\Domain\Customer\Model;

use ChessableBanking\Domain\Currency\Entity\Currency;

class Balance
{

    private float $amount; // TODO: It should not be float; use dedicated library for precise prices management
    private Currency $currency;

    public function __construct(float $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}

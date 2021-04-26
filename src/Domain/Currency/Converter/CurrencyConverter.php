<?php

namespace ChessableBanking\Domain\Currency\Converter;

use ChessableBanking\Domain\Currency\Entity\Currency;
use RuntimeException;

class CurrencyConverter // TODO: Consider different strategies, f.e. for rounding the amounts or exchange commission
{

    public function convert(float $amount, Currency $from, Currency $to): float
    {
        if ($from->getRateToDefaultCurrency() <= 0 || $to->getRateToDefaultCurrency() <= 0) {
            throw new RuntimeException('Wrong configuration, currency rate must be positive');
        }

        if ($from->getId() === $to->getId()) {
            return $amount;
        }

        //TODO: Use dedicated library, make it precise
        return floor(100 * $amount * $from->getRateToDefaultCurrency() / $to->getRateToDefaultCurrency()) / 100;
    }
}

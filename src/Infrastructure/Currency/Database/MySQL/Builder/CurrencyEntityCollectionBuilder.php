<?php

namespace ChessableBanking\Infrastructure\Currency\Database\MySQL\Builder;

use ChessableBanking\Domain\Currency\Entity\Currency;
use stdClass;

class CurrencyEntityCollectionBuilder
{

    /**
     * @param iterable|stdClass[] $currencies
     * @return Currency[]
     */
    public function build(iterable $currencies): array
    {
        $entitiesCollection = [];
        foreach ($currencies as $currency) {
            $entitiesCollection[] = new Currency(
                $currency->id ?? '',
                $currency->name ?? '',
                $currency->iso_4217_code ?? '',
                $currency->rate_to_default_currency ?? 0.0,
                $currency->is_default ?? false
            );
        }

        return $entitiesCollection;
    }
}

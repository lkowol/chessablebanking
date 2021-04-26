<?php

namespace ChessableBanking\Infrastructure\Country\Database\MySQL\Builder;

use ChessableBanking\Domain\Country\Entity\Country;
use stdClass;

class CountryEntityCollectionBuilder
{

    /**
     * @param iterable|stdClass[] $countries
     * @return Country[]
     */
    public function build(iterable $countries): array
    {
        $entitiesCollection = [];
        foreach ($countries as $country) {
            $entitiesCollection[] = new Country(
                $country->id ?? '',
                $country->name ?? '',
                $country->iso_3166_alpha_3_code ?? ''
            );
        }

        return $entitiesCollection;
    }
}

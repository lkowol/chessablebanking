<?php

namespace ChessableBanking\Infrastructure\Location\Database\MySQL\Builder;

use ChessableBanking\Domain\Country\Entity\Country;
use ChessableBanking\Domain\Location\Entity\Location;
use stdClass;

class LocationEntityBuilder
{

    public function build(stdClass $location, Country $country): Location
    {
        return new Location(
            $location->id ?? '',
            $location->city ?? '',
            $location->street ?? '',
            $location->building_number ?? '',
            $location->apartment_number ?? null,
            $location->postal_code ?? '',
            $country
        );
    }
}

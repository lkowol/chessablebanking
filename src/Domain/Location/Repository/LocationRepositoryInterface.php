<?php

namespace ChessableBanking\Domain\Location\Repository;

use ChessableBanking\Domain\Location\Entity\Location;
use ChessableBanking\Domain\Location\Exception\LocationValidationException;

interface LocationRepositoryInterface
{

    public function findAll(): array;

    public function find(string $id): ?Location;

    /**
     * @param Location $location
     * @return Location
     * @throws LocationValidationException
     */
    public function create(Location $location): Location;
}

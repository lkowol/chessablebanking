<?php

namespace ChessableBanking\Domain\Country\Repository;

use ChessableBanking\Domain\Country\Entity\Country;

interface CountryRepositoryInterface
{

    /**
     * @return Country[]
     */
    public function findAll(): array;

    public function find(string $id): ?Country;
}

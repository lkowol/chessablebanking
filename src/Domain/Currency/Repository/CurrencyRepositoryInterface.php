<?php

namespace ChessableBanking\Domain\Currency\Repository;

use ChessableBanking\Domain\Currency\Entity\Currency;

interface CurrencyRepositoryInterface
{

    public function findAll(): array;

    public function find(string $id): ?Currency;
}

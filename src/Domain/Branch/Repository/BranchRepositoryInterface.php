<?php

namespace ChessableBanking\Domain\Branch\Repository;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Exception\BranchValidationException;

interface BranchRepositoryInterface
{

    /**
     * @param Branch $branch
     * @return Branch
     * @throws BranchValidationException
     */
    public function create(Branch $branch): Branch;

    /**
     * @return Branch[]
     */
    public function findAll(): array;

    public function find(string $id): ?Branch;

    public function findByName(string $name): ?Branch;
}

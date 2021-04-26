<?php

namespace ChessableBanking\Application\Migration\Registry;

use ChessableBanking\Application\Migration\MigrationInterface;

class MigrationRegistry
{

    /**
     * @var MigrationInterface[]
     */
    private array $migrations = [];

    public function register(MigrationInterface $migration): void
    {
        $this->migrations[] = $migration;
    }

    public function registerMany(iterable $migrations): void
    {
        foreach ($migrations as $migration) {
            $this->register($migration);
        }
    }

    /**
     * @return MigrationInterface[]
     */
    public function getNotInstalled(): array
    {
        return array_filter($this->migrations, fn(MigrationInterface $migration) => !$migration->isInstalled());
    }
}

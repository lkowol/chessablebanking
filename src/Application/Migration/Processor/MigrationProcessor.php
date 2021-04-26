<?php

namespace ChessableBanking\Application\Migration\Processor;

use ChessableBanking\Application\Migration\Exception\MigrationException;
use ChessableBanking\Application\Migration\Registry\MigrationRegistry;
use Psr\Log\LoggerInterface;

class MigrationProcessor
{

    private MigrationRegistry $migrationRegistry;
    private LoggerInterface $logger;

    public function __construct(MigrationRegistry $migrationRegistry, LoggerInterface $logger)
    {
        $this->migrationRegistry = $migrationRegistry;
        $this->logger = $logger;
    }

    public function process(): void
    {
        foreach ($this->migrationRegistry->getNotInstalled() as $migration) {
            try {
                $migration->install();
            } catch (MigrationException $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}

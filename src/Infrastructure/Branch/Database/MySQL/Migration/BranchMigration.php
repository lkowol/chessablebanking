<?php

namespace ChessableBanking\Infrastructure\Branch\Database\MySQL\Migration;

use ChessableBanking\Application\Migration\Exception\MigrationException;
use ChessableBanking\Application\Migration\MigrationInterface;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use Exception;

class BranchMigration implements MigrationInterface
{

    private MySQLConnection $mySQLConnection;

    public function __construct(MySQLConnection $mySQLConnection)
    {
        $this->mySQLConnection = $mySQLConnection;
    }

    public function install(): void
    {
        try {
            $this->createTable();
        } catch (Exception $e) {
            throw new MigrationException($e->getMessage());
        }
    }

    public function isInstalled(): bool
    {
        return $this->mySQLConnection->checkIfTableExists('branch');
    }

    private function createTable(): void
    {
        $this->mySQLConnection->query(
            "CREATE TABLE `branch` (
                    `id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)', 
                    `name` VARCHAR(64) NOT NULL, 
                    `location_id` CHAR(36) NOT NULL, 
                    PRIMARY KEY(`id`),
                    UNIQUE INDEX `name` (`name`), 
                    UNIQUE INDEX `location_id` (`location_id`), 
                    CONSTRAINT `location` FOREIGN KEY (`location_id`)
                        REFERENCES `location`(`id`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
    }
}

<?php

namespace ChessableBanking\Infrastructure\Location\Database\MySQL\Migration;

use ChessableBanking\Application\Migration\Exception\MigrationException;
use ChessableBanking\Application\Migration\MigrationInterface;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use Exception;

class LocationMigration implements MigrationInterface
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
        return $this->mySQLConnection->checkIfTableExists('location');
    }

    private function createTable(): void
    {
        $this->mySQLConnection->query(
            "CREATE TABLE `location` (
                    `id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)', 
                    `city` VARCHAR(255) NOT NULL, 
                    `street` VARCHAR(255) NOT NULL, 
                    `building_number` VARCHAR(32) NOT NULL, 
                    `apartment_number` VARCHAR(32) DEFAULT NULL, 
                    `postal_code` VARCHAR(8) NOT NULL, 
                    `country_id` CHAR(36) NOT NULL, 
                    PRIMARY KEY(`id`),
                    CONSTRAINT `country` FOREIGN KEY (`country_id`)
                        REFERENCES `country`(`id`)
              ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
    }
}

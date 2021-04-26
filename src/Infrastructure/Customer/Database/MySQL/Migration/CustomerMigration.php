<?php

namespace ChessableBanking\Infrastructure\Customer\Database\MySQL\Migration;

use ChessableBanking\Application\Migration\Exception\MigrationException;
use ChessableBanking\Application\Migration\MigrationInterface;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use Exception;

class CustomerMigration implements MigrationInterface
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
        return $this->mySQLConnection->checkIfTableExists('customer');
    }

    private function createTable(): void
    {
        $this->mySQLConnection->query(
            "CREATE TABLE `customer` (
                    `id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)', 
                    `branch_id` CHAR(36) NOT NULL, 
                    `name` VARCHAR(255) NOT NULL, 
                    `balance_amount` DECIMAL (15,2) NOT NULL, 
                    `balance_currency_id` CHAR(36) NOT NULL, 
                    PRIMARY KEY(`id`),
                    CONSTRAINT `currency` FOREIGN KEY (`balance_currency_id`)
                        REFERENCES `currency`(`id`),
                    CONSTRAINT `branch` FOREIGN KEY (`branch_id`)
                        REFERENCES `branch`(`id`)
              ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
    }
}

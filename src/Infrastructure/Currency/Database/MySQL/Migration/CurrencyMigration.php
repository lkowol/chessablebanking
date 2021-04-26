<?php

namespace ChessableBanking\Infrastructure\Currency\Database\MySQL\Migration;

use ChessableBanking\Application\Migration\Exception\MigrationException;
use ChessableBanking\Application\Migration\MigrationInterface;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use Exception;
use PDO;

class CurrencyMigration implements MigrationInterface
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
            // Adding few currencies, there is no need to do it using GUI for now
            //TODO: Validation, there must be only one default currency
            $this->addCurrency('Euro', 'EUR', 1, true);
            $this->addCurrency('Polish złoty', 'PLN', 0.22, false);
            $this->addCurrency('Norwegian krone', 'NOK', 0.1, false);
        } catch (Exception $e) {
            throw new MigrationException($e->getMessage());
        }
    }

    public function isInstalled(): bool
    {
        return $this->mySQLConnection->checkIfTableExists('currency');
    }

    private function createTable(): void
    {
        $this->mySQLConnection->query(
            "CREATE TABLE `currency` (
                    `id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)', 
                    `name` VARCHAR(255) NOT NULL, 
                    `iso_4217_code` VARCHAR(3) NOT NULL, 
                    `rate_to_default_currency` DECIMAL (15,2) NOT NULL, 
                    `is_default` BOOLEAN NOT NULL DEFAULT FALSE, 
                    PRIMARY KEY(`id`),
                    INDEX `is_default` (`is_default`),
                    UNIQUE INDEX `name` (`name`), 
                    UNIQUE INDEX `iso_4217_code` (`iso_4217_code`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
    }

    private function addCurrency(
        string $currencyName,
        string $isoCode,
        float $rateToDefaultCurrency,
        bool $isDefault
    ): void {
        $statement = $this->mySQLConnection->prepare("
            INSERT INTO `currency` (`id`, `name`, `iso_4217_code`, `rate_to_default_currency`, `is_default`) VALUES
	        (UUID(), :name, :isoCode, :rateToDefaultCurrency, :isDefault);");
        $statement->bindParam(':name', $currencyName);
        $statement->bindParam(':isoCode', $isoCode);
        $statement->bindParam(':rateToDefaultCurrency', $rateToDefaultCurrency);
        $statement->bindParam(':isDefault', $isDefault, PDO::PARAM_BOOL);
        $statement->execute();
    }
}

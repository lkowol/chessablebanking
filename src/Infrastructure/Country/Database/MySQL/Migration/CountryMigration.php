<?php

namespace ChessableBanking\Infrastructure\Country\Database\MySQL\Migration;

use ChessableBanking\Application\Migration\Exception\MigrationException;
use ChessableBanking\Application\Migration\MigrationInterface;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use Exception;

class CountryMigration implements MigrationInterface
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
            // Adding few countries, there is no need to do it using GUI for now
            $this->addCountry('Spain', 'ESP');
            $this->addCountry('Poland', 'POL');
            $this->addCountry('United Kingdom', 'GBR');
            $this->addCountry('Argentina', 'ARG');
        } catch (Exception $e) {
            throw new MigrationException($e->getMessage());
        }
    }

    public function isInstalled(): bool
    {
        return $this->mySQLConnection->checkIfTableExists('country');
    }

    private function createTable(): void
    {
        //TODO: Country name should be language aware and should allow multiple languages
        $this->mySQLConnection->query(
            "CREATE TABLE `country` (
                    `id` CHAR(36) NOT NULL COMMENT '(DC2Type:guid)', 
                    `name` VARCHAR(64) NOT NULL, 
                    `iso_3166_alpha_3_code` VARCHAR(3) NOT NULL, 
                    PRIMARY KEY(`id`),
                    UNIQUE INDEX `name` (`name`), 
                    UNIQUE INDEX `iso_3166_alpha_3_code` (`iso_3166_alpha_3_code`)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
    }

    private function addCountry(string $countryName, string $iso3166Alpha3Code): void
    {
        $statement = $this->mySQLConnection->prepare("
            INSERT INTO `country` (`id`, `name`, `iso_3166_alpha_3_code`) VALUES
	        (UUID(), :countryName, :isoCode);");

        $statement->bindParam(':countryName', $countryName);
        $statement->bindParam(':isoCode', $iso3166Alpha3Code);
        $statement->execute();
    }
}

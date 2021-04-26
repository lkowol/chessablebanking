<?php

namespace ChessableBanking\Infrastructure\Country\Database\MySQL\Repository;

use ChessableBanking\Domain\Country\Entity\Country;
use ChessableBanking\Domain\Country\Repository\CountryRepositoryInterface;
use ChessableBanking\Infrastructure\Country\Database\MySQL\Builder\CountryEntityCollectionBuilder;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use PDO;

class MySQLCountryRepository implements CountryRepositoryInterface
{

    private MySQLConnection $mySQLConnection;
    private CountryEntityCollectionBuilder $collectionBuilder;

    public function __construct(MySQLConnection $mySQLConnection, CountryEntityCollectionBuilder $collectionBuilder)
    {
        $this->mySQLConnection = $mySQLConnection;
        $this->collectionBuilder = $collectionBuilder;
    }

    public function findAll(): array
    {
        $statement = $this->mySQLConnection
            ->query("SELECT `id`, `name`, `iso_3166_alpha_3_code` FROM `country`");
        $statement->execute();
        $result = (array) $statement->fetchAll(PDO::FETCH_OBJ);
        //TODO: Add validation of compatibility MySQL data with domain object,
        // now there is no need (countries are being added manually during migration)

        return $this->collectionBuilder->build($result);
    }

    public function find(string $id): ?Country
    {
        $statement = $this->mySQLConnection
            ->prepare("SELECT `id`, `name`, `iso_3166_alpha_3_code` FROM `country` WHERE `id` = :id");
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (empty($result)) {
            return null;
        }

        return new Country(
            $result->id ?? '',
            $result->name ?? '',
            $result->iso_3166_alpha_3_code ?? ''
        );
    }
}

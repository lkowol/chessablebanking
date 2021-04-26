<?php

namespace ChessableBanking\Infrastructure\Currency\Database\MySQL\Repository;

use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Currency\Repository\CurrencyRepositoryInterface;
use ChessableBanking\Infrastructure\Currency\Database\MySQL\Builder\CurrencyEntityCollectionBuilder;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use PDO;

class MySQLCurrencyRepository implements CurrencyRepositoryInterface
{

    private MySQLConnection $mySQLConnection;
    private CurrencyEntityCollectionBuilder $collectionBuilder;

    public function __construct(MySQLConnection $mySQLConnection, CurrencyEntityCollectionBuilder $collectionBuilder)
    {
        $this->mySQLConnection = $mySQLConnection;
        $this->collectionBuilder = $collectionBuilder;
    }

    public function findAll(): array
    {
        $statement = $this->mySQLConnection
            ->query("SELECT `id`, `name`, `iso_4217_code`, `rate_to_default_currency`, `is_default` FROM `currency`");
        $statement->execute();
        $result = (array) $statement->fetchAll(PDO::FETCH_OBJ);
        //TODO: Add validation of compatibility MySQL data with domain object,
        // now there is no need (currencies are being added manually during migration)

        return $this->collectionBuilder->build($result);
    }

    public function find(string $id): ?Currency
    {
        $statement = $this->mySQLConnection
            ->prepare("SELECT `id`, `name`, `iso_4217_code`, `rate_to_default_currency`, `is_default` 
                FROM `currency` WHERE `id` = :id");
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        if (empty($result)) {
            return null;
        }

        return new Currency(
            $result->id ?? '',
            $result->name ?? '',
            $result->iso_4217_code ?? '',
            $result->rate_to_default_currency ?? 0.0,
            $result->is_default ?? false,
        );
    }
}

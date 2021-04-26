<?php


namespace ChessableBanking\Infrastructure\Database\MySQL\Connection;

use PDO;

/*
 * Connection holder for PDO, as I was told not to use ORM (but in widely interpretable way),
 * so I was not sure if I should:
 * - not to use any ORM libraries like orm-pack (even though it contains another tools like DBAL),
 * - not to use f.e. Doctrine and propose own, raw-query based object hydration,
 * - use another idea like DAO
 *
 * And that's the simples way (Hope so it also meet the requirements, it was really unclear)
 */

class MySQLConnection extends PDO
{

    private string $databaseName;

    public function __construct(
        string $databaseHost,
        int $databasePort,
        string $databaseName,
        string $userName,
        string $password,
        string $charset
    ) {
        parent::__construct($this->constructPdoDsn(
            $databaseHost,
            (string)$databasePort,
            $databaseName,
            $charset
        ), $userName, $password);
        $this->databaseName = $databaseName;
    }

    private function constructPdoDsn(
        string $databaseHost,
        string $databasePort,
        string $databaseName,
        string $charset
    ): string {
        return sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $databaseHost,
            $databasePort,
            $databaseName,
            $charset
        );
    }

    public function checkIfTableExists(string $tableName): bool
    {
        $statement = $this->prepare("SELECT COUNT(*) AS `count` 
            FROM `information_schema`.`tables` 
            WHERE table_schema = :tableSchema AND table_name = :tableName;");
        $statement->bindParam(':tableName', $tableName);
        $statement->bindParam(':tableSchema', $this->databaseName);
        $statement->execute();
        $record = $statement->fetch(PDO::FETCH_OBJ);

        return $record->count > 0;
    }
}

<?php

namespace ChessableBanking\Infrastructure\Database\MySQL\Security;

use ChessableBanking\Application\Security\TransactionInsurerInterface;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;

class TransactionInsurer implements TransactionInsurerInterface
{

    private MySQLConnection $mySQLConnection;

    public function __construct(MySQLConnection $mySQLConnection)
    {
        $this->mySQLConnection = $mySQLConnection;
    }

    public function beginTransaction(): void
    {
        $this->mySQLConnection->beginTransaction();
    }

    public function commit(): void
    {
        $this->mySQLConnection->commit();
    }

    public function rollback(): void
    {
        $this->mySQLConnection->rollBack();
    }
}

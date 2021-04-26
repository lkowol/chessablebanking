<?php

namespace ChessableBanking\Application\Security;

interface TransactionInsurerInterface
{

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;
}

<?php

namespace ChessableBanking\Infrastructure\Branch\Database\MySQL\Repository;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use ChessableBanking\Infrastructure\Branch\Database\MySQL\Processor\BranchResultProcessor;
use ChessableBanking\Infrastructure\Branch\Database\MySQL\Validator\BranchValidator;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use PDO;

class MySQLBranchRepository implements BranchRepositoryInterface
{

    private MySQLConnection $mySQLConnection;
    private BranchResultProcessor $branchResultProcessor;
    private BranchValidator $branchValidator;

    public function __construct(
        MySQLConnection $mySQLConnection,
        BranchResultProcessor $branchResultProcessor,
        BranchValidator $branchValidator
    ) {
        $this->mySQLConnection = $mySQLConnection;
        $this->branchResultProcessor = $branchResultProcessor;
        $this->branchValidator = $branchValidator;
    }

    public function create(Branch $branch): Branch
    {
        $this->branchValidator->validate($branch);
        $statement = $this->mySQLConnection->prepare("
            INSERT INTO `branch` (`id`, `name`, `location_id`)
                VALUES
	        (:id, :name, :locationId);");
        $statement->bindValue(':id', $branch->getId());
        $statement->bindValue(':name', $branch->getName());
        $statement->bindValue(':locationId', $branch->getLocation()->getId());

        $statement->execute();

        return $branch;
    }

    public function findAll(): array
    {
        $statement = $this->mySQLConnection
            ->query("SELECT 
                `id`, `name`, `location_id` 
            FROM `branch`");
        $statement->execute();

        return $this->branchResultProcessor->processCollection((array) $statement->fetchAll(PDO::FETCH_OBJ));
    }

    public function find(string $id): ?Branch
    {
        $statement = $this->mySQLConnection
            ->prepare("SELECT 
                `id`, `name`, `location_id` 
            FROM `branch` WHERE `id` = :id");
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);
        if (empty($result)) {
            return null;
        }

        return $this->branchResultProcessor->processRow($result);
    }

    public function findByName(string $name): ?Branch
    {
        $statement = $this->mySQLConnection
            ->prepare("SELECT 
                `id`, `name`, `location_id` 
            FROM `branch` WHERE `name` = :name");
        $statement->bindParam(':name', $name);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);
        if (empty($result)) {
            return null;
        }

        return $this->branchResultProcessor->processRow($result);
    }
}

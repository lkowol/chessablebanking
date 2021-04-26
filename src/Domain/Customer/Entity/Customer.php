<?php


namespace ChessableBanking\Domain\Customer\Entity;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Customer\Model\Balance;

class Customer
{

    private string $id;
    private string $name;
    private Branch $branch;
    private Balance $balance;

    public function __construct(string $id, string $name, Branch $branch, Balance $balance)
    {
        $this->id = $id;
        $this->name = $name;
        $this->branch = $branch;
        $this->balance = $balance;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBranch(): Branch
    {
        return $this->branch;
    }

    public function getBalance(): Balance
    {
        return $this->balance;
    }
}

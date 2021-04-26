<?php

namespace ChessableBanking\Infrastructure\Report\Database\MySQL;

use ChessableBanking\Application\Report\BranchesWithCustomersWithSpecifiedBalanceReportInterface;
use ChessableBanking\Application\Report\Model\BranchName;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use PDO;

class BranchesWithCustomersWithSpecifiedBalanceReport implements BranchesWithCustomersWithSpecifiedBalanceReportInterface
{

    private MySQLConnection $mySQLConnection;

    public function __construct(MySQLConnection $mySQLConnection)
    {
        $this->mySQLConnection = $mySQLConnection;
    }

    /**
     * @return BranchName[]
     */
    public function getData(int $customersAmount, float $minimalBalance): array
    {
        $statement = $this->mySQLConnection
            ->prepare("SELECT 
                `branch`.`name` AS `branch_name`,
                COUNT(`customer`.`id`) AS `customers_amount`
            FROM `branch`
            JOIN `customer` ON `customer`.`branch_id` = `branch`.`id`
            LEFT JOIN `currency` `customer_balance_currency` 
                ON `customer`.`balance_currency_id` = `customer_balance_currency`.`id`
            WHERE COALESCE(`customer`.`balance_amount` / `customer_balance_currency`.`rate_to_default_currency`, 0) 
                      > :minimalBalance
            GROUP BY `branch`.`id` HAVING `customers_amount` >= :customerAmount;
        ");
        $statement->bindParam(':minimalBalance', $minimalBalance);
        $statement->bindParam(':customerAmount', $customersAmount, PDO::PARAM_INT);

        $statement->execute();

        $result = (array) $statement->fetchAll(PDO::FETCH_OBJ);

        return $this->convertResult($result);
    }

    /**
     * @return BranchName[]
     */
    private function convertResult(array $result): array
    {
        $branchesWithBalance = [];

        foreach ($result as $row) {
            $branchesWithBalance[] = new BranchName(
                $row->branch_name ?? '',
            );
        }

        return $branchesWithBalance;
    }
}

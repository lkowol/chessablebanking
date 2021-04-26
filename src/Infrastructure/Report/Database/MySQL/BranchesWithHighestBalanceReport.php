<?php

namespace ChessableBanking\Infrastructure\Report\Database\MySQL;

use ChessableBanking\Application\Report\BranchesWithHighestBalanceReportInterface;
use ChessableBanking\Application\Report\Model\BranchWithBalance;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use PDO;
use Psr\Log\LoggerInterface;

class BranchesWithHighestBalanceReport implements BranchesWithHighestBalanceReportInterface
{

    private MySQLConnection $mySQLConnection;
    private LoggerInterface $logger;

    public function __construct(MySQLConnection $mySQLConnection, LoggerInterface $logger)
    {
        $this->mySQLConnection = $mySQLConnection;
        $this->logger = $logger;
    }

    /**
     * @return BranchWithBalance[]
     */
    public function getData(): array
    {
        $statement = $this->mySQLConnection
            ->query("SELECT 
                `branch`.`name` AS `branch_name`,
                MAX(COALESCE(`customer`.`balance_amount` * `customer_balance_currency`.`rate_to_default_currency`, 0)) 
                    AS `balance_in_default_currency`,
                `default_currency`.`name` AS `currency_name`
            FROM `branch`
            LEFT JOIN `customer` ON `customer`.`branch_id` = `branch`.`id`
            LEFT JOIN `currency` `customer_balance_currency` 
                ON `customer`.`balance_currency_id` = `customer_balance_currency`.`id`
            JOIN `currency` `default_currency` ON `default_currency`.`is_default` = 1
            GROUP BY `branch`.`id`, `default_currency`.`id`;");

        if (false === $statement) {
            $this->logger->error('Could not generate report');
            return [];
        }

        $statement->execute();
        $result = (array) $statement->fetchAll(PDO::FETCH_OBJ);

        return $this->convertResult($result);
    }

    /**
     * @return BranchWithBalance[]
     */
    private function convertResult(array $result): array
    {
        $branchesWithBalance = [];

        foreach ($result as $row) {
            $branchesWithBalance[] = new BranchWithBalance(
                $row->branch_name ?? '',
                $row->balance_in_default_currency ?? 0.0,
                $row->currency_name ?? ''
            );
        }

        return $branchesWithBalance;
    }
}

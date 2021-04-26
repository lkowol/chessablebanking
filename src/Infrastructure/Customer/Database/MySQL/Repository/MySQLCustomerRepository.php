<?php

namespace ChessableBanking\Infrastructure\Customer\Database\MySQL\Repository;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\CustomerValidationException;
use ChessableBanking\Domain\Customer\Repository\CustomerRepositoryInterface;
use ChessableBanking\Infrastructure\Customer\Database\MySQL\Processor\CustomerResultProcessor;
use ChessableBanking\Infrastructure\Customer\Database\MySQL\Validator\CustomerValidator;
use ChessableBanking\Infrastructure\Database\MySQL\Connection\MySQLConnection;
use Exception;
use PDO;
use Psr\Log\LoggerInterface;

class MySQLCustomerRepository implements CustomerRepositoryInterface
{

    private MySQLConnection $mySQLConnection;
    private CustomerResultProcessor $customerResultProcessor;
    private CustomerValidator $customerValidator;
    private LoggerInterface $logger;

    public function __construct(
        MySQLConnection $mySQLConnection,
        CustomerResultProcessor $customerResultProcessor,
        CustomerValidator $customerValidator,
        LoggerInterface $logger
    ) {

        $this->mySQLConnection = $mySQLConnection;
        $this->customerResultProcessor = $customerResultProcessor;
        $this->customerValidator = $customerValidator;
        $this->logger = $logger;
    }

    public function findByBranch(Branch $branch): array
    {
        $statement = $this->mySQLConnection
            ->query("SELECT `id`, `branch_id`, `name`, `balance_amount`, `balance_currency_id` FROM `customer`");
        $statement->execute();

        return $this->customerResultProcessor->processCollection((array) $statement->fetchAll(PDO::FETCH_OBJ));
    }

    public function find(string $id): ?Customer
    {
        $statement = $this->mySQLConnection
            ->prepare(
                "SELECT `id`, `branch_id`, `name`, `balance_amount`, `balance_currency_id` 
                        FROM `customer` WHERE `id` = :id"
            );
        $statement->bindParam(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);
        if (empty($result)) {
            return null;
        }

        return $this->customerResultProcessor->processRow($result);
    }

    /**
     * @param Customer $customer
     * @return Customer
     * @throws CustomerValidationException
     */
    public function create(Customer $customer): Customer
    {
        $this->customerValidator->validate($customer);
        $statement = $this->mySQLConnection->prepare("
            INSERT INTO `customer` (`id`, `branch_id`, `name`, `balance_amount`, `balance_currency_id`)
                VALUES
	        (:id, :branchId, :name, :balanceAmount, :balanceCurrencyId);");
        $statement->bindValue(':id', $customer->getId());
        $statement->bindValue(':branchId', $customer->getBranch()->getId());
        $statement->bindValue(':name', $customer->getName());
        $statement->bindValue(':balanceAmount', $customer->getBalance()->getAmount());
        $statement->bindValue(':balanceCurrencyId', $customer->getBalance()->getCurrency()->getId());

        $statement->execute();

        return $customer;
    }

    public function transfer(Customer $sender, Customer $receiver, float $senderAmount, float $receiverAmount): void
    {
        try {
            $this->mySQLConnection->beginTransaction();
            $this->changeBalance($sender, $senderAmount);
            $this->changeBalance($receiver, $receiverAmount);
            $this->mySQLConnection->commit();
        } catch (Exception $e) {
            $this->logger->emergency($e->getMessage());
            $this->mySQLConnection->rollBack();
        }
    }

    private function changeBalance(Customer $customer, float $amount): void
    {
        $statement = $this->mySQLConnection->prepare("
           UPDATE `customer` SET `balance_amount` = `balance_amount` + :amount
           WHERE `id` = :id");
        $statement->bindValue(':id', $customer->getId());
        $statement->bindValue(':amount', $amount);
        $statement->execute();
    }
}

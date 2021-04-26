<?php

namespace ChessableBanking\Infrastructure\Customer\Database\MySQL\Processor;

use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use ChessableBanking\Domain\Currency\Repository\CurrencyRepositoryInterface;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\CustomerValidationException;
use ChessableBanking\Domain\Customer\Validator\CustomerValidationError;
use ChessableBanking\Infrastructure\Customer\Database\MySQL\Builder\CustomerEntityBuilder;
use ChessableBanking\Infrastructure\Customer\Database\MySQL\Validator\CustomerValidator;
use Psr\Log\LoggerInterface;
use stdClass;

class CustomerResultProcessor
{

    private CustomerEntityBuilder $customerEntityBuilder;
    private CustomerValidator $customerValidator;
    private LoggerInterface $logger;
    private BranchRepositoryInterface $branchRepository;
    private CurrencyRepositoryInterface $currencyRepository;

    public function __construct(
        CustomerEntityBuilder $customerEntityBuilder,
        CustomerValidator $customerValidator,
        LoggerInterface $logger,
        BranchRepositoryInterface $branchRepository,
        CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->customerEntityBuilder = $customerEntityBuilder;
        $this->customerValidator = $customerValidator;
        $this->logger = $logger;
        $this->branchRepository = $branchRepository;
        $this->currencyRepository = $currencyRepository;
    }

    public function processRow(stdClass $customer): ?Customer
    {
        $branch = $this->branchRepository->find($customer->branch_id ?? null);
        if (null === $branch) {
            $this->logger->error(sprintf('Unable to provide branch for customer with ID %s', $customer->id ?? ''));
            return null;
        }

        $balanceCurrency = $this->currencyRepository->find($customer->balance_currency_id ?? null);
        if (null === $balanceCurrency) {
            $this->logger->error(sprintf(
                'Unable to provide balance currency for customer with ID %s',
                $customer->id ?? ''
            ));

            return null;
        }

        $customerEntity = $this->customerEntityBuilder->build($customer, $branch, $balanceCurrency);
        try {
            $this->customerValidator->validate($customerEntity);
        } catch (CustomerValidationException $e) {
            $this->logger->error(
                sprintf(
                    'Unable to build correct customer from MySQL data for customer with ID %s due to: %s',
                    $customer->id ?? '',
                    join(
                        ', ',
                        array_map(fn(CustomerValidationError $error) => $error->getErrorMessage(), $e->getErrors())
                    )
                )
            );
            return null;
        }

        return $customerEntity;
    }

    /**
     * @param array|stdClass[] $customers
     * @return Customer[]
     */
    public function processCollection(array $customers): array
    {
        $customerEntities = [];
        foreach ($customers as $customer) {
            $customerEntity = $this->processRow($customer);
            if (null === $customerEntity) {
                continue;
                // Just skip & log this, something was tried to be converted, but definitely not the correct entity
            }

            $customerEntities[] = $customerEntity;
        }

        return $customerEntities;
    }
}

<?php

namespace ChessableBanking\Domain\Customer;

use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Model\Balance;
use ChessableBanking\Domain\Customer\Repository\CustomerRepositoryInterface;
use ChessableBanking\Domain\Customer\Validator\CustomerValidator;

class CustomerService
{

    private CustomerRepositoryInterface $customerRepository;
    private CustomerValidator $customerValidator;

    public function __construct(CustomerRepositoryInterface $customerRepository, CustomerValidator $customerValidator)
    {
        $this->customerRepository = $customerRepository;
        $this->customerValidator = $customerValidator;
    }

    /**
     * @throws Exception\CustomerValidationException
     */
    public function create(
        string $id,
        string $name,
        float $balanceAmount,
        Currency $balanceCurrency,
        Branch $branch
    ): Customer {
        $customer = new Customer(
            $id,
            $name,
            $branch,
            new Balance($balanceAmount, $balanceCurrency)
        );
        $this->customerValidator->validate($customer);
        return $this->customerRepository->create($customer);
    }

    /**
     * @return Customer[]
     */
    public function provideForBranch(Branch $branch): array
    {
        return $this->customerRepository->findByBranch($branch);
    }

    public function provideForId(string $customerId): ?Customer
    {
        return $this->customerRepository->find($customerId);
    }
}

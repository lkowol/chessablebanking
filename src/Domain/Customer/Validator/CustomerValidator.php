<?php


namespace ChessableBanking\Domain\Customer\Validator;

use ChessableBanking\Domain\Branch\Repository\BranchRepositoryInterface;
use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Currency\Repository\CurrencyRepositoryInterface;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\CustomerValidationException;
use ChessableBanking\Domain\Customer\Repository\CustomerRepositoryInterface;

class CustomerValidator
{

    private CustomerRepositoryInterface $customerRepository;
    private CurrencyRepositoryInterface $currencyRepository;
    private BranchRepositoryInterface $branchRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CurrencyRepositoryInterface $currencyRepository,
        BranchRepositoryInterface $branchRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->currencyRepository = $currencyRepository;
        $this->branchRepository = $branchRepository;
    }

    /**
     * @param Customer $customer
     * @param bool $validateForCreation
     * @throws CustomerValidationException
     */
    public function validate(Customer $customer, bool $validateForCreation = false): void
    {
        $errors = [];
        if (empty($customer->getId())) {
            $errors[] = new CustomerValidationError('id', 'Id must not be empty');
        }

        $nameLength = strlen($customer->getName());
        if ($nameLength < 3 || $nameLength > 255) {
            $errors[] = new CustomerValidationError('name', 'Name must be between 3 and 255 characters long');
        }

        if (null === $this->currencyRepository->find($customer->getBalance()->getCurrency()->getId())) {
            $errors[] = new CustomerValidationError('balance', 'Not existing currency given');
        }

        if (null === $this->branchRepository->find($customer->getBranch()->getId())) {
            $errors[] = new CustomerValidationError('branch', 'Not existing branch given');
        }

        if ($customer->getBalance()->getAmount() < 0.0) { // TODO: Consider negative balance (f.e. credit)
            $errors[] = new CustomerValidationError('balance', 'Balance must be positive');
        }

        if (strlen(substr((string) strrchr((string) $customer->getBalance()->getAmount(), '.'), 1))
            > Currency::PRECISION) {
            $errors[] = new CustomerValidationError(
                'balance',
                sprintf('Balance amount precision must not be greater than %d', Currency::PRECISION)
            );
        }

        if ($validateForCreation && null !== $this->customerRepository->find($customer->getId())) {
            $errors[] = new CustomerValidationError('id', 'Id must be unique');
        }

        if (!empty($errors)) {
            throw new CustomerValidationException($errors);
        }
    }
}

<?php

namespace ChessableBanking\Application\Customer;

use ChessableBanking\Application\Customer\Model\Customer;
use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Currency\Repository\CurrencyRepositoryInterface;
use ChessableBanking\Domain\Customer\CustomerService;
use ChessableBanking\Domain\Customer\Exception\CustomerValidationException;
use ChessableBanking\Domain\Customer\Validator\CustomerValidationError;
use Symfony\Component\Uid\Uuid;

class CustomerProcessor
{

    private CustomerService $customerService;
    private CurrencyRepositoryInterface $currencyRepository;

    public function __construct(
        CustomerService $customerService,
        CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->customerService = $customerService;
        $this->currencyRepository = $currencyRepository;
    }

    public function create(Customer $customerModel): array
    {
        try {
            $this->customerService->create(
                Uuid::v6(),
                $customerModel->getName(),
                $customerModel->getBalanceAmount(),
                $this->currencyRepository->find($customerModel->getBalanceCurrencyId())
                ?? new Currency('', '', '', 0.0, false),
                $customerModel->getBranch()
            );

            return [];
        } catch (CustomerValidationException $e) {
            return array_combine(
                array_map(fn(CustomerValidationError $e) => $e->getFieldName(), $e->getErrors()),
                array_map(fn(CustomerValidationError $e) => $e->getErrorMessage(), $e->getErrors())
            );
        }
    }
}

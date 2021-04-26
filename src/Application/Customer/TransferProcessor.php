<?php

namespace ChessableBanking\Application\Customer;

use ChessableBanking\Application\Customer\Model\Transfer;
use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Country\Entity\Country;
use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\TransferValidationException;
use ChessableBanking\Domain\Customer\Model\Balance;
use ChessableBanking\Domain\Customer\Repository\CustomerRepositoryInterface;
use ChessableBanking\Domain\Customer\TransferService;
use ChessableBanking\Domain\Customer\Validator\TransferValidationError;
use ChessableBanking\Domain\Location\Entity\Location;

class TransferProcessor
{

    private TransferService $transferService;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        TransferService $transferService,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->transferService = $transferService;
        $this->customerRepository = $customerRepository;
    }

    public function transfer(Transfer $transfer): array
    {
        try {
            $this->transferService->transfer(
                $this->customerRepository->find($transfer->getSenderCustomerId())
                ?? $this->getEmptyCustomer(),
                $this->customerRepository->find($transfer->getReceiverCustomerId())
                ?? $this->getEmptyCustomer(),
                $transfer->getAmount()
            );
            return [];
        } catch (TransferValidationException $e) {
            return array_combine(
                array_map(fn(TransferValidationError $e) => $e->getFieldName(), $e->getErrors()),
                array_map(fn(TransferValidationError $e) => $e->getErrorMessage(), $e->getErrors())
            );
        }
    }

    private function getEmptyCustomer(): Customer
    {
        return new Customer(
            '',
            '',
            new Branch('', '', new Location(
                '',
                '',
                '',
                '',
                '',
                '',
                new Country('', '', '')
            )),
            new Balance(0.0, $this->getEmptyCurrency())
        );
    }

    private function getEmptyCurrency(): Currency
    {
        return new Currency('', '', '', 0.0, false);
    }
}

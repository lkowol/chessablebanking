<?php


namespace ChessableBanking\Domain\Customer;

use ChessableBanking\Domain\Currency\Converter\CurrencyConverter;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\TransferValidationException;
use ChessableBanking\Domain\Customer\Repository\CustomerRepositoryInterface;
use ChessableBanking\Domain\Customer\Validator\TransferValidator;

class TransferService
{

    private CurrencyConverter $currencyConverter;
    private TransferValidator $transferValidator;
    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        TransferValidator $transferValidator,
        CurrencyConverter $currencyConverter,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->currencyConverter = $currencyConverter;
        $this->transferValidator = $transferValidator;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Customer $sender
     * @param Customer $receiver
     * @param float $amount
     * @throws TransferValidationException
     */
    public function transfer(Customer $sender, Customer $receiver, float $amount): void
    {
        $this->transferValidator->validate($sender, $receiver, $amount);
        $senderCurrency = $sender->getBalance()->getCurrency();
        $receiverCurrency = $sender->getBalance()->getCurrency();

        $receiverAmount = $this->currencyConverter->convert($amount, $senderCurrency, $receiverCurrency);

        $this->customerRepository->transfer(
            $sender,
            $receiver,
            -$amount,
            $receiverAmount
        );
    }
}

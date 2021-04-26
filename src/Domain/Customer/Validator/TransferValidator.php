<?php

namespace ChessableBanking\Domain\Customer\Validator;

use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\TransferValidationException;
use ChessableBanking\Domain\Customer\Repository\CustomerRepositoryInterface;

class TransferValidator
{

    private CustomerRepositoryInterface $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
    ) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Customer $sender
     * @param Customer $receiver
     * @param float $amount
     * @throws TransferValidationException
     */
    public function validate(Customer $sender, Customer $receiver, float $amount): void
    {
        $errors = [];
        if (empty($sender->getId())) {
            $errors[] = new TransferValidationError('sender', 'Sender is incorrect');
        }

        if (empty($receiver->getId())) {
            $errors[] = new TransferValidationError('receiver', 'Receiver is incorrect');
        }

        if ($sender->getId() === $receiver->getId()) {
            $errors[] = new TransferValidationError('sender', 'You cannot transfer to yourself');
        }

        $sender = $this->customerRepository->find($sender->getId());
        if (null === $sender) {
            $errors[] = new TransferValidationError('sender', 'Sender does not exist');
        }

        if (null === $this->customerRepository->find($receiver->getId())) {
            $errors[] = new TransferValidationError('receiver', 'Receiver does not exist');
        }

        if (null !== $sender && $sender->getBalance()->getAmount() < $amount) {
            $errors[] = new TransferValidationError('sender', 'Sender balance is too low');
        }

        if (!empty($errors)) {
            throw new TransferValidationException($errors);
        }
    }
}

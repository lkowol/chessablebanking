<?php

namespace ChessableBanking\Application\Customer\Model;

class Transfer
{

    private string $senderCustomerId;
    private string $receiverCustomerId;
    private float $amount;

    public function __construct(
        string $senderCustomerId,
        string $receiverCustomerId,
        float $amount
    ) {
        $this->senderCustomerId = $senderCustomerId;
        $this->receiverCustomerId = $receiverCustomerId;
        $this->amount = $amount;
    }

    public function getSenderCustomerId(): string
    {
        return $this->senderCustomerId;
    }

    public function getReceiverCustomerId(): string
    {
        return $this->receiverCustomerId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}

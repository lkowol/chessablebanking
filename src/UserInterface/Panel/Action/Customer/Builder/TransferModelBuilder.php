<?php

namespace ChessableBanking\UserInterface\Panel\Action\Customer\Builder;

use ChessableBanking\Application\Customer\Model\Transfer;
use ChessableBanking\Domain\Customer\Entity\Customer;

class TransferModelBuilder
{

    public function build(array $formData, Customer $senderCustomer): Transfer
    {
        return new Transfer(
            $senderCustomer->getId(),
            $formData['toCustomerId'] ?? '',
            (float) $formData['amount'] ?? 0.0,
        );
    }
}

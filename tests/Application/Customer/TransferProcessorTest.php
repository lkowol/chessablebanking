<?php

namespace ChessableBanking\Tests\Application\Customer;

use ChessableBanking\Application\Customer\Model\Transfer;
use ChessableBanking\Application\Customer\TransferProcessor;
use ChessableBanking\Domain\Customer\Entity\Customer;
use ChessableBanking\Domain\Customer\Exception\TransferValidationException;
use ChessableBanking\Domain\Customer\Repository\CustomerRepositoryInterface;
use ChessableBanking\Domain\Customer\TransferService;
use ChessableBanking\Domain\Customer\Validator\TransferValidationError;
use PHPUnit\Framework\TestCase;

class TransferProcessorTest extends TestCase
{

    public function testTransferProcessor(): void
    {
        $senderCustomer = $this->createMock(Customer::class);
        $receiverCustomer = $this->createMock(Customer::class);

        $transfer = $this->createMock(Transfer::class);
        $transfer->expects($this->once())->method('getSenderCustomerId')->willReturn('theSenderCustomerId');
        $transfer->expects($this->once())->method('getReceiverCustomerId')->willReturn('theReceiverCustomerId');
        $transfer->expects($this->once())->method('getAmount')->willReturn(123.12);

        $transferService = $this->createMock(TransferService::class);
        $transferService->expects($this->once())->method('transfer')->with(
            $senderCustomer,
            $receiverCustomer,
            123.12
        );

        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->exactly(2))->method('find')
            ->withConsecutive(
                ['theSenderCustomerId'],
                ['theReceiverCustomerId']
            )->willReturnOnConsecutiveCalls(
                $senderCustomer,
                $receiverCustomer
            );

        $transferProcessor = new TransferProcessor(
            $transferService,
            $customerRepository
        );

        $this->assertEmpty($transferProcessor->transfer($transfer));
    }

    public function testTransferProcessorWithErrors(): void
    {
        $transferService = $this->createMock(TransferService::class);
        $transferService->expects($this->once())->method('transfer')->willThrowException(
            new TransferValidationException([
                new TransferValidationError('theField', 'theMessage')
            ])
        );

        $transferProcessor = new TransferProcessor(
            $transferService,
            $this->createMock(CustomerRepositoryInterface::class)
        );

        $result = $transferProcessor->transfer($this->createMock(Transfer::class));

        $this->assertCount(1, $result);
        $this->assertEquals('theMessage', $result['theField']);
    }
}

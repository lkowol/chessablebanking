<?php

namespace ChessableBanking\Tests\Application\Customer;

use ChessableBanking\Application\Customer\CustomerProcessor;
use ChessableBanking\Application\Customer\Model\Customer;
use ChessableBanking\Domain\Branch\Entity\Branch;
use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Currency\Repository\CurrencyRepositoryInterface;
use ChessableBanking\Domain\Customer\CustomerService;
use ChessableBanking\Domain\Customer\Exception\CustomerValidationException;
use ChessableBanking\Domain\Customer\Validator\CustomerValidationError;
use PHPUnit\Framework\TestCase;

class CustomerProcessorTest extends TestCase
{

    public function testProcessor(): void
    {
        $branch = $this->createMock(Branch::class);
        $currency = $this->createMock(Currency::class);

        $customer = $this->createMock(Customer::class);
        $customer->expects($this->once())->method('getName')->willReturn('theName');
        $customer->expects($this->once())->method('getBalanceAmount')->willReturn(111.22);
        $customer->expects($this->once())->method('getBalanceCurrencyId')->willReturn('theBalanceCurrencyId');
        $customer->expects($this->once())->method('getBranch')->willReturn($branch);

        $customerService = $this->createMock(CustomerService::class);
        $customerService->expects($this->once())->method('create')
            ->with(
                $this->anything(),
                'theName',
                111.22,
                $currency,
                $branch
            );

        $currencyRepository = $this->createMock(CurrencyRepositoryInterface::class);
        $currencyRepository->expects($this->once())->method('find')->with('theBalanceCurrencyId')->willReturn($currency);

        $customerProcessor = new CustomerProcessor($customerService, $currencyRepository);
        $this->assertEmpty($customerProcessor->create($customer));
    }

    public function testProcessorWithErrors(): void
    {
        $customer = $this->createMock(Customer::class);

        $customerService = $this->createMock(CustomerService::class);
        $customerService->expects($this->once())->method('create')->willThrowException(
            new CustomerValidationException([
                new CustomerValidationError('theField', 'theMessage')
            ])
        );
        $currencyRepository = $this->createMock(CurrencyRepositoryInterface::class);
        $customerProcessor = new CustomerProcessor($customerService, $currencyRepository);

        $result = $customerProcessor->create($customer);

        $this->assertCount(1, $result);
        $this->assertEquals('theMessage', $result['theField']);
    }
}

<?php

namespace ChessableBanking\Application\Report;

use ChessableBanking\Application\Report\Model\BranchName;

interface BranchesWithCustomersWithSpecifiedBalanceReportInterface
{

    /**
     * @return BranchName[]
     */
    public function getData(int $customersAmount, float $minimalBalance): array;
}

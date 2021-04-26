<?php

namespace ChessableBanking\Application\Report;

use ChessableBanking\Application\Report\Model\BranchWithBalance;

interface BranchesWithHighestBalanceReportInterface
{

    /**
     * @return BranchWithBalance[]
     */
    public function getData(): array;
}

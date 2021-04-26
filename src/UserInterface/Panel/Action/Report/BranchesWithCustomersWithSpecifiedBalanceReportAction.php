<?php


namespace ChessableBanking\UserInterface\Panel\Action\Report;

use ChessableBanking\Application\Report\BranchesWithCustomersWithSpecifiedBalanceReportInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;

class BranchesWithCustomersWithSpecifiedBalanceReportAction extends AbstractAction
{

    /**
     * @Route("/panel/report/branchesWithCustomersWithSpecifiedBalance", name="branches_with_customers_with_specified_balance_report")
     */
    public function index(BranchesWithCustomersWithSpecifiedBalanceReportInterface $report): Response
    {
        $this->addPanel('Panel/Action/Report/templates/branchesWithCustomersWithSpecifiedBalanceReport.html.twig', [
            'branches' => $report->getData(3, 50000.0)
        ]);

        return $this->renderAction('Branches with customers with specified balance report');
    }
}

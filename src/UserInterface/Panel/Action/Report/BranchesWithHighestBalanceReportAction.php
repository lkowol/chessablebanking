<?php


namespace ChessableBanking\UserInterface\Panel\Action\Report;

use ChessableBanking\Application\Report\BranchesWithHighestBalanceReportInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;

class BranchesWithHighestBalanceReportAction extends AbstractAction
{

    /**
     * @Route("/panel/report/branchesWithHighestBalance", name="branches_with_highest_balance_report")
     */
    public function index(BranchesWithHighestBalanceReportInterface $report): Response
    {
        $this->addPanel('Panel/Action/Report/templates/branchesWithHighestBalanceReport.html.twig', [
            'branches' => $report->getData()
        ]);

        return $this->renderAction('Branches with highest balance report');
    }
}

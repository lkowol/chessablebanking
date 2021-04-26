<?php


namespace ChessableBanking\UserInterface\Panel\Action\Customer;

use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\Application\Branch\CurrentBranchService;
use ChessableBanking\Domain\Customer\CustomerService;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class ListAction extends AbstractAction
{

    /**
     * @Route("/panel/customer/list", name="customer_list")
     */
    public function index(CurrentBranchService $currentBranchService, CustomerService $customerService): Response
    {
        $currentBranch = $currentBranchService->get();
        if (null === $currentBranch) {
            return $this->redirectToRouteWithMessage('index', [], 'Branch is not chosen');
        }

        $this->addPanel('Panel/Action/Customer/templates/list.html.twig', [
            'customers' => $customerService->provideForBranch($currentBranch)
        ]);

        return $this->renderAction('Customer list');
    }
}

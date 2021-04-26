<?php


namespace ChessableBanking\UserInterface\Panel\Action\Customer;

use ChessableBanking\Application\Customer\TransferProcessor;
use ChessableBanking\UserInterface\Panel\Action\Customer\Builder\TransferModelBuilder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\Application\Branch\CurrentBranchService;
use ChessableBanking\Domain\Customer\CustomerService;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class TransferAction extends AbstractAction
{

    /**
     * @Route("/panel/customer/transfer/{fromCustomerId}", name="customer_transfer")
     */
    public function index(
        ?string $fromCustomerId = null, // A little hack making it nullable, due to Symfony twig issues with path()
        Request $request,
        CurrentBranchService $currentBranchService,
        CustomerService $customerService,
        TransferModelBuilder $transferModelBuilder,
        TransferProcessor $transferProcessor
    ): Response {
        $currentBranch = $currentBranchService->get();
        if (null === $currentBranch) {
            return $this->redirectToRouteWithMessage('index', [], 'Branch is not chosen');
        }

        if (null === $fromCustomerId) {
            return $this->redirectToRouteWithMessage('customer_list', [], 'No sender customer ID given');
        }

        $fromCustomer = $customerService->provideForId($fromCustomerId);
        if (null === $fromCustomer) {
            return $this->redirectToRouteWithMessage(
                'customer_list',
                [],
                sprintf('Customer with ID %s does not exist', $fromCustomerId)
            );
        }

        $transferForm = $this->prepareTransferForm();
        $transferForm->handleRequest($request);
        if ($transferForm->isSubmitted() && $transferForm->isValid()) {
            $transferForm = $this->prepareTransferForm($transferForm->getData());
            $transferModel = $transferModelBuilder->build($transferForm->getData(), $fromCustomer);
            $errors = $transferProcessor->transfer($transferModel);
            foreach ($errors as $error) {
                $transferForm->addError(new FormError($error));
            }

            if (empty($errors)) {
                return $this->redirectToRouteWithMessage('customer_list', [], 'Transfer was made');
            }
        }


        $this->addPanel('Panel/Action/Customer/templates/transfer.html.twig', [
            'transferForm' => $transferForm->createView()
        ]);

        return $this->renderAction('Customer list');
    }

    private function prepareTransferForm(?array $data = null): FormInterface
    {
        return $this->createFormBuilder($data)
            ->add('toCustomerId', TextType::class, ['label' => 'Receiver customer ID'])
            ->add('amount', TextType::class, ['label' => 'Transfer amount'])
            ->add('save', SubmitType::class, ['label' => 'Transfer'])
            ->getForm();
    }
}

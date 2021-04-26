<?php

namespace ChessableBanking\UserInterface\Panel\Action\Customer;

use ChessableBanking\Application\Branch\CurrentBranchService;
use ChessableBanking\Application\Customer\CustomerProcessor;
use ChessableBanking\Domain\Currency\Entity\Currency;
use ChessableBanking\Domain\Currency\Repository\CurrencyRepositoryInterface;
use ChessableBanking\UserInterface\Panel\Action\Customer\Builder\CustomerModelBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use ChessableBanking\UserInterface\Panel\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class CreateAction extends AbstractAction
{

    /**
     * @Route("/panel/customer/create", name="customer_create")
     */
    public function index(
        Request $request,
        CurrencyRepositoryInterface $currencyRepository,
        CustomerModelBuilder $customerModelBuilder,
        CustomerProcessor $customerProcessor,
        CurrentBranchService $currentBranchService
    ): Response {
        $currentBranch = $currentBranchService->get();
        if (null === $currentBranch) {
            return $this->redirectToRouteWithMessage('index', [], 'Branch is not chosen');
        }

        $currencies = $this->prepareCurrenciesChoices($currencyRepository->findAll());
        $customerForm = $this->prepareCustomerForm($currencies);
        $customerForm->handleRequest($request);
        if ($customerForm->isSubmitted() && $customerForm->isValid()) {
            $customerForm = $this->prepareCustomerForm($currencies, $customerForm->getData());
            $customer = $customerModelBuilder->build($customerForm->getData(), $currentBranch);
            $errors = $customerProcessor->create($customer);
            foreach ($errors as $error) {
                $customerForm->addError(new FormError($error));
            }

            if (empty($errors)) {
                return $this->redirectToRouteWithMessage('customer_list', [], 'Customer was created');
            }
        }

        $this->addPanel('Panel/Action/Customer/templates/create.html.twig', [
            'customerForm' => $customerForm->createView()
        ]);

        return $this->renderAction('Create customer');
    }

    private function prepareCustomerForm(array $currencies, ?array $data = null): FormInterface
    {
        return $this->createFormBuilder($data)
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('balanceAmount', TextType::class, ['label' => 'Balance amount'])
            ->add('balanceCurrency', ChoiceType::class, ['label' => 'Balance currency', 'choices' => $currencies])
            ->add('save', SubmitType::class, ['label' => 'Create customer'])
            ->getForm();
    }

    /**
     * @param array|Currency[] $currenciesCollection
     * @return array
     */
    private function prepareCurrenciesChoices(array $currenciesCollection): array
    {
        return array_combine(
            array_map(fn(Currency $currency) => $currency->getName(), $currenciesCollection),
            array_map(fn(Currency $currency) => $currency->getId(), $currenciesCollection),
        );
    }
}

<?php

namespace ChessableBanking\UserInterface\Panel\Action\Branch;

use ChessableBanking\Application\Branch\BranchWithLocationProcessor;
use ChessableBanking\Domain\Country\Repository\CountryRepositoryInterface;
use ChessableBanking\UserInterface\Panel\Action\Branch\Builder\BranchWithLocationBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Routing\Annotation\Route;
use ChessableBanking\Domain\Country\Entity\Country;
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
     * @Route("/panel/branch/create", name="branch_create")
     */
    public function index(
        Request $request,
        CountryRepositoryInterface $countryRepository,
        BranchWithLocationBuilder $branchWithLocationBuilder,
        BranchWithLocationProcessor $branchWithLocationProcessor
    ): Response {
        $countries = $this->prepareCountriesChoices($countryRepository->findAll());
        $branchForm = $this->prepareBranchForm($countries);
        $branchForm->handleRequest($request);
        if ($branchForm->isSubmitted() && $branchForm->isValid()) {
            $branchForm = $this->prepareBranchForm($countries, $branchForm->getData());
            $branchWithLocation = $branchWithLocationBuilder->build($branchForm->getData());
            $errors = $branchWithLocationProcessor->create($branchWithLocation);
            foreach ($errors as $error) {
                $branchForm->addError(new FormError($error));
            }

            if (empty($errors)) {
                return $this->redirectToRouteWithMessage('branch_list', [], 'Branch was created');
            }
        }

        $this->addPanel('Panel/Action/Branch/templates/create.html.twig', [
            'branchForm' => $branchForm->createView()
        ]);

        return $this->renderAction('Create branch');
    }

    private function prepareBranchForm(array $countries, ?array $data = null): FormInterface
    {
        return $this->createFormBuilder($data)
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('city', TextType::class, ['label' => 'City'])
            ->add('street', TextType::class, ['label' => 'Street'])
            ->add('buildingNumber', TextType::class, ['label' => 'Building Number'])
            ->add('apartmentNumber', TextType::class, ['label' => 'Apartment Number', 'required' => false])
            ->add('postalCode', TextType::class, ['label' => 'Postal Code'])
            ->add('country', ChoiceType::class, ['label' => 'Country', 'choices' => $countries])
            ->add('save', SubmitType::class, ['label' => 'Create branch'])
            ->getForm();
    }

    /**
     * @param array|Country[] $countriesCollection
     * @return array
     */
    private function prepareCountriesChoices(array $countriesCollection): array
    {
        return array_combine(
            array_map(fn(Country $country) => $country->getName(), $countriesCollection),
            array_map(fn(Country $country) => $country->getId(), $countriesCollection),
        );
    }
}

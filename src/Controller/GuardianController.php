<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Guardian;
use App\Entity\Security\Roles;
use App\Form\GuardianType;
use App\Repository\GuardianRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/guardian')]
final class GuardianController extends AbstractController
{
    #[IsGranted(Roles::READ_GUARDIAN)]
    #[Route(name: 'app_guardian_index', methods: [Request::METHOD_GET])]
    public function index(
        Request            $request,
        GuardianRepository $guardianRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        $pagination = $paginator->paginate(
            $guardianRepository->createQueryBuilder('guardian')
                ->leftJoin('guardian.kids', 'kids')
                ->addSelect('kids')
                ->orderBy('guardian.name'),
            (int)$request->query->get('page', 1) ?: 1,
            10
        );

        return $this->render('guardian/index.html.twig', [
            'pager' => $pagination,
        ]);
    }

    #[IsGranted(Roles::CREATE_GUARDIAN)]
    #[Route('/new', name: 'app_guardian_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $guardian = new Guardian();
        $form = $this->createForm(GuardianType::class, $guardian);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($guardian);
            $entityManager->flush();

            return $this->redirectToRoute('app_guardian_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('guardian/new.html.twig', [
            'guardian' => $guardian,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::UPDATE_GUARDIAN)]
    #[Route('/{id}/edit', name: 'app_guardian_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, Guardian $guardian, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GuardianType::class, $guardian);
        $form->add('renew_uuid', CheckboxType::class, [
            'mapped' => false,
            'required' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('renew_uuid')->getData()) {
                $guardian->setUuid(Uuid::v7());
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_guardian_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('guardian/edit.html.twig', [
            'guardian' => $guardian,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::DELETE_GUARDIAN)]
    #[Route('/{id}', name: 'app_guardian_delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request, Guardian $guardian, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $guardian->getId(), $request->getPayload()->getString('_token'))) {
            $guardian->getKids()->clear();
            $entityManager->remove($guardian);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_guardian_index', [], Response::HTTP_SEE_OTHER);
    }
}

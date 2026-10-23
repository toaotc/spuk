<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Kid;
use App\Entity\Security\Roles;
use App\Form\KidType;
use App\Repository\KidRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/kid')]
final class KidController extends AbstractController
{
    #[IsGranted(Roles::READ_KID)]
    #[Route(name: 'app_kid_index', methods: [Request::METHOD_GET])]
    public function index(
        Request            $request,
        KidRepository      $kidRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        $pagination = $paginator->paginate(
            $kidRepository->createQueryBuilder('kid')
                ->leftJoin('kid.kidGroup', 'kid_group')
                ->addSelect('kid_group')
                ->leftJoin('kid.guardians', 'guardians')
                ->addSelect('guardians')
                ->orderBy('kid.name'),
            (int)$request->query->get('page', 1) ?: 1,
            10
        );

        return $this->render('kid/index.html.twig', [
            'pager' => $pagination,
        ]);
    }

    #[IsGranted(Roles::CREATE_KID)]
    #[Route('/new', name: 'app_kid_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $kid = new Kid();
        $form = $this->createForm(KidType::class, $kid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($kid);
            $entityManager->flush();

            return $this->redirectToRoute('app_kid_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('kid/new.html.twig', [
            'kid' => $kid,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::UPDATE_KID)]
    #[Route('/{id}/edit', name: 'app_kid_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, Kid $kid, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(KidType::class, $kid);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_kid_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('kid/edit.html.twig', [
            'kid' => $kid,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::DELETE_KID)]
    #[Route('/{id}', name: 'app_kid_delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request, Kid $kid, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $kid->getId(), $request->getPayload()->getString('_token'))) {
            $kid->getGuardians()->clear();
            $kid->getAppointments()->clear();
            $entityManager->remove($kid);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_kid_index', [], Response::HTTP_SEE_OTHER);
    }
}

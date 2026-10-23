<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\KidGroup;
use App\Entity\Security\Roles;
use App\Form\KidGroupType;
use App\Repository\KidGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/kidgroup')]
final class KidGroupController extends AbstractController
{
    #[IsGranted(Roles::READ_KID_GROUP)]
    #[Route(name: 'app_kidgroup_index', methods: [Request::METHOD_GET])]
    public function index(
        Request            $request,
        KidGroupRepository $kidGroupRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        $pagination = $paginator->paginate(
            $kidGroupRepository->createQueryBuilder('kid_group')
                ->leftJoin('kid_group.kids', 'kids')
                ->addSelect('kids')
                ->orderBy('kid_group.name'),
            (int)$request->query->get('page', 1) ?: 1,
            10
        );

        return $this->render('kidgroup/index.html.twig', [
            'pager' => $pagination,
        ]);
    }

    #[IsGranted(Roles::CREATE_KID_GROUP)]
    #[Route('/new', name: 'app_kidgroup_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $kidGroup = new KidGroup();
        $form = $this->createForm(KidGroupType::class, $kidGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($kidGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_kidgroup_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('kidgroup/new.html.twig', [
            'kidgroup' => $kidGroup,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::UPDATE_KID_GROUP)]
    #[Route('/{id}/edit', name: 'app_kidgroup_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, KidGroup $kidGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(KidGroupType::class, $kidGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_kidgroup_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('kidgroup/edit.html.twig', [
            'kidgroup' => $kidGroup,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::DELETE_KID_GROUP)]
    #[Route('/{id}', name: 'app_kidgroup_delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request, KidGroup $kidGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$kidGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($kidGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_kidgroup_index', [], Response::HTTP_SEE_OTHER);
    }
}

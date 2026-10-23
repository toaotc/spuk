<?php

namespace App\Controller\Security;

use App\Entity\Security\Roles;
use App\Entity\Security\User;
use App\Form\Security\UserType;
use App\Repository\Security\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/security/user')]
final class UserController extends AbstractController
{
    #[IsGranted(Roles::READ_USER)]
    #[Route(name: 'app_security_user_index', methods: ['GET'])]
    public function index(
        Request $request,
        UserRepository $userRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        $pagination = $paginator->paginate(
            $userRepository->createQueryBuilder('user'),
            (int)$request->query->get('page', 1) ?: 1,
            10
        );

        return $this->render('security/user/index.html.twig', [
            'pager' => $pagination,
        ]);
    }

    #[IsGranted(Roles::CREATE_USER)]
    #[Route('/new', name: 'app_security_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('plain_password', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plain_password')->getData()));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_security_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::UPDATE_USER)]
    #[Route('/{id}/edit', name: 'app_security_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->add('plain_password', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false,
            'required' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $plainPassword = $form->get('plain_password')->getData()) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_security_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::DELETE_USER)]
    #[Route('/{id}', name: 'app_security_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_security_user_index', [], Response::HTTP_SEE_OTHER);
    }
}

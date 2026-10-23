<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Security\Roles;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/appointment')]
final class AppointmentController extends AbstractController
{
    #[IsGranted(Roles::READ_APPOINTMENT)]
    #[Route(name: 'app_appointment_index', methods: [Request::METHOD_GET])]
    public function index(
        Request $request,
        AppointmentRepository $appointmentRepository,
        PaginatorInterface $paginator,
    ): Response
    {
        $qb = $appointmentRepository->createQueryBuilder('appointment')
            ->leftJoin('appointment.attendees', 'attendee')
            ->addSelect('attendee')
            ->orderBy('appointment.startAt', 'DESC');

        $form = $this->createForm(FormType::class, [], [
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'method' => Request::METHOD_GET,
        ])
            ->add('from', DateType::class, ['required' => false])
            ->add('to', DateType::class, ['required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $from = $form->get('from')->getData()) {
                $from = DateTimeImmutable::createFromMutable($from)
                    ->setTimezone(new \DateTimeZone('UTC'));
                $qb
                    ->andWhere('appointment.startAt >= DATE(:date_start)')
                    ->setParameter('date_start', $from);
            }

            if (null !== $to = $form->get('to')->getData()) {
                $to = DateTimeImmutable::createFromMutable($to)
                    ->setTimezone(new \DateTimeZone('UTC'))
                    ->modify('+1 day')
                ;
                $qb
                    ->andWhere('appointment.endAt <= DATE(:date_end)')
                    ->setParameter('date_end', $to);
            }
        }

        $pagination = $paginator->paginate(
            $qb,
            (int)$request->query->get('page', 1) ?: 1,
            10
        );

        return $this->render('appointment/index.html.twig', [
            'pager' => $pagination,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted(Roles::CREATE_APPOINTMENT)]
    #[Route('/new', name: 'app_appointment_new', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($appointment);
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appointment/new.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::UPDATE_APPOINTMENT)]
    #[Route('/{id}/edit', name: 'app_appointment_edit', methods: [Request::METHOD_GET, Request::METHOD_POST])]
    public function edit(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appointment/edit.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    #[IsGranted(Roles::DELETE_APPOINTMENT)]
    #[Route('/{id}', name: 'app_appointment_delete', methods: [Request::METHOD_POST])]
    public function delete(Request $request, Appointment $appointment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$appointment->getId(), $request->getPayload()->getString('_token'))) {
            $appointment->getAttendees()->clear();
            $entityManager->remove($appointment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_appointment_index', [], Response::HTTP_SEE_OTHER);
    }
}

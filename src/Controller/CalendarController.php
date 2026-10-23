<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Guardian;
use App\Entity\Kid;
use App\Repository\AppointmentRepository;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CalendarController extends AbstractController
{
    #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
    #[Route('/calendar/{uuid}.{_format<html|ics>}', name: 'app_calendar_show', methods: [Request::METHOD_GET])]
    public function show(Request $request, Guardian $guardian, AppointmentRepository $appointmentRepository): Response
    {
        if (!$guardian->isActive()) {
            throw $this->createNotFoundException('Calendar not found');
        }

        $appointments = $appointmentRepository->createQueryBuilder('appointment')
            ->join('appointment.attendees', 'attendee')
            ->join('attendee.guardians', 'guardian')
            ->addSelect('attendee')
            ->where('guardian = :guardian')
            ->andWhere('appointment.active = true')
            ->andWhere('attendee.active = true')
            ->setParameter('guardian', $guardian)
            ->orderBy('appointment.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        if ($request->getRequestFormat() === 'html') {
            return $this->render('calendar/show.html.twig', [
                'appointments' => $appointments,
                'guardian' => $guardian,
            ]);
        }

        $calendar = new Calendar();
        foreach ($appointments as $appointment) {
            $event = new Event(new UniqueIdentifier((string)$appointment->getId()))
                ->setSummary($appointment->getTitle())
                ->setOccurrence(new TimeSpan(
                    new DateTime($appointment->getStartAt(), false),
                    new DateTime($appointment->getEndAt(), false),
                ));

            $kids = array_uintersect(
                $appointment->getAttendees()->getValues(),
                $guardian->getKids()->getValues(),
                fn($a, $b) => $a->getId() <=> $b->getId()
            );

            $description = implode(', ', array_map(fn(Kid $kid) => $kid->getName(), $kids)) . PHP_EOL;
            $description .= $appointment->getDescription();
            $event->setDescription($description);

            $calendar->addEvent($event);
        }

        $componentFactory = new CalendarFactory();

        return new Response(
            (string)$componentFactory->createCalendar($calendar),
            headers: [
                'Content-Type' => 'text/calendar',
            ]
        );
    }
}

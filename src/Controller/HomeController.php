<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED)]
    #[Route('/', name: 'app_home', methods: [Request::METHOD_GET])]
    public function index(): Response
    {
        return $this->render('_static/home.html.twig');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbonnementController extends AbstractController
{
    /**
     * @Route("abonnement")
     */
    public function index(): Response
    {
        return $this->render('frontend/afficherAbonnement.html.twig', [
        ]);

    }

    /**
     * @Route("abonnementback")
     */
    public function indexback(): Response
    {
        return $this->render('backend/afficherAbonnement.html.twig', [
        ]);
    }
}

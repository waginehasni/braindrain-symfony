<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class EvenementController extends AbstractController
{
    /**
     * @Route("evenement")
     */
    public function index(): Response
    {
        return $this->render('/frontend/afficherEvenement.html.twig',[
        ]);
    }
}

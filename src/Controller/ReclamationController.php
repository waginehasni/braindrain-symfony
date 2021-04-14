<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    /**
     * @Route("reclamation")
     */
    public function index(): Response
    {
        return $this->render('frontend/afficherReclamation.html.twig',[
        ]);
    }
}

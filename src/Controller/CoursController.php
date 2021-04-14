<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{
    /**
     * @Route("cours")
     */
    public function index(): Response
    {
        return $this->render('frontend/afficherCours.html.twig',[
        ]);
    }
    /**
     * @Route("coursback")
     */
    public function indexback(): Response
    {
        return $this->render('backend/afficherCours.html.twig',[
        ]);
    }
}

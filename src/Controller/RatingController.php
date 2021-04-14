<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    /**
     * @Route("rating")
     */
    public function index(): Response
    {
        return $this->render('frontend/afficherRating.html.twig',[
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SponsorController extends AbstractController
{
    /**
     * @Route("")
     */
    public function index(): Response
    {
        return $this->render('frontend/accueil.html.twig',[
            'i' => 'abc',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("")
     * @Route("accueil")
     */
    public function index(): Response
    {
        return $this->render('frontend/accueil.html.twig',[

        ]);
    }

    /**
     *
     * @Route("accueilback")
     */
    public function indexback(): Response
    {
        return $this->render('backend/accueilback.html.twig',[

        ]);
    }
}

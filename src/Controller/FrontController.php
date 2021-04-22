<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    /**
     * @Route("/front", name="front")
     */
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }
    /**
     * @Route("/afficher2", name="afficher2")
     */
    public function afficher2(): Response
    {
        $listCours=$this->getDoctrine()->getRepository(Cours::class)->findAll();
        //dd($listOffres);
        return $this->render('front/afficher2.html.twig', [
            'controller_name' => 'CoursController','cours'=>$listCours
        ]);
    }
    /**
     * @Route("/afficher3", name="afficher3")
     */
    public function afficher3(): Response
    {
        $listCours=$this->getDoctrine()->getRepository(Reservation::class)->findAll();
        //dd($listOffres);
        return $this->render('front/afficher3.html.twig', [
            'controller_name' => 'ReservationController','reservation'=>$listCours
        ]);
    }
}

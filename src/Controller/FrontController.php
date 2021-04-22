<?php

namespace App\Controller;

use App\Entity\Produits;
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
        $listProduits=$this->getDoctrine()->getRepository(Produits::class)->findAll();
        //dd($listProduits);
        return $this->render('front/afficher2.html.twig', [
            'controller_name' => 'ProduitsController','produits'=>$listProduits
        ]);
    }
}

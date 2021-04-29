<?php

namespace App\Controller;

use App\Entity\Offres;
use App\Entity\Produits;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


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
        $listOffres=$this->getDoctrine()->getRepository(Offres::class)->findAll();
        //dd($listOffres);
        return $this->render('front/afficher2.html.twig', [
            'controller_name' => 'OffresController','offres'=>$listOffres
        ]);
    }

    /**
     * @Route("/afficher4", name="afficher4")
     */
    public function afficher4(): Response
    {
        $listCours=$this->getDoctrine()->getRepository(Cours::class)->findAll();
        //dd($listOffres);
        return $this->render('front/afficher4.html.twig', [
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
    /**
     * @Route("/afficher5", name="afficher5")
     */
    public function afficher5(PaginatorInterface $paginator,Request $request): Response
    {
        $listProduits=$this->getDoctrine()->getRepository(Produits::class)->findAll();
        //dd($listProduits);
        $listProduits = $paginator->paginate(
        // Doctrine Query, not results
            $listProduits,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('front/afficher5.html..twig', [
            'controller_name' => 'ProduitsController','produits'=>$listProduits
        ]);
    }

    /**
     * @Route("")
     * @Route("accueil" ,name="accueil")
     */
    public function indexfront(): Response
    {
        return $this->render('front/index.html.twig', [

        ]);
    }
}

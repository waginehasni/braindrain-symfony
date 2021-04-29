<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/produits")
 */
class ProduitsController extends AbstractController
{

    /**
     * @Route("/TrierParQ", name="TrierParQ")
     */


    public function TrierParQuantites(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Produits::class);
        $produits = $repository->findByquantites();

        return $this->render('produits/index.html.twig', [
            'produits' =>  $produits,
        ]);
    }
    /**
     * @Route("/", name="produits_index", methods={"GET"})
     */


    public function index(ProduitRepository $produitRepository ,Request $request): Response
    {
        $produit =$produitRepository->findAll();

        return $this->render('produits/index.html.twig', [
            'produits' =>  $produit
        ]);
    }

    /**
     * @Route("/new", name="produits_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $produit = new Produits();
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produits_index');
        }

        return $this->render('produits/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produits_show", methods={"GET"})
     */
    public function show(Produits $produit): Response
    {
        return $this->render('produits/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="produits_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produits $produit): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produits_index');
        }

        return $this->render('produits/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="produits_delete", methods={"POST"})
     */
    public function delete(Request $request, Produits $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produits_index');
    }

    /**
     * @Route("/export/stat", name="stat", methods={"GET"})
     */
    public function Statistic(ProduitRepository $produitRepository ,Request $request): Response
    {
        #$produits = $produitRepository->findAll();
        $catType= ['nutritions', 'materiel','vetements']; #, 'Suivi', 'Msg', 'Technical', 'Posts'];
        $catColor = ['#36CAAB', '#B370CF','#34495E']; #, '#34495E', '#B370CF', '#AC5353', '#CFD4D8'];
        $catNut= count($produitRepository->findBy(["type" =>"nutritions"]) )  ;
        $catMat = count($produitRepository->findBy(["type" =>"materiel"]) ) ;
        $catVet = count($produitRepository->findBy(["type" =>"vetements"]) ) ;

        #$catSuivi = count($reclamationRepository->findBy(["idCat" => "Suivi"]) ) ;
        #$catMsg= count($reclamationRepository->findBy(["idCat" =>"Msg"]) )  ;
        #$catTechnical = count($reclamationRepository->findBy(["idCat" =>"Technical"]) ) ;
        #$catPosts = count($reclamationRepository->findBy(["idCat" => "Posts"]) ) ;
        $catCount = [ $catNut, $catMat,$catVet]; #,$categSuivi, $categMsg, $categTechnical, $categPosts];

        return $this->render('produits/statistic.html.twig',
            ['catType' => json_encode($catType),
                'catColor' => json_encode($catColor),
                'catCount' => json_encode($catCount),


            ]);
    }
}

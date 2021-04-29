<?php

namespace App\Controller;

use App\Entity\Rating;
use App\Form\RatingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    /**
     * @Route("/back/rating")
     */
    public function indexBack(): Response
    {
        return $this->render('back/afficherRating.html.twig', [
            'ratings' => $this->getDoctrine()->getRepository(Rating::class)->findAll(),
        ]);
    }

    /**
     * @Route("rating")
     */
    public function index(): Response
    {
        return $this->render('front/afficherRating.html.twig', [
            'ratings' => $this->getDoctrine()->getRepository(Rating::class)->findAll(),
        ]);
    }

    /**
     * @Route("rating/ajouter")
     */
    public function ajout(Request $request)
    {
        $rating = new Rating();


        $this->createForm(RatingType::class);
        $form = $this->createForm(RatingType::class, $rating)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {

            $rating = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();

            return $this->redirect('/rating');
        }

        return $this->render('front/ajouterRating.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("rating/{idRating}/modifier")
     */
    public function modifier(Request $request, $idRating)
    {
        $rating = $this->getDoctrine()->getRepository(Rating::class)->find($idRating);

        $this->createForm(RatingType::class);
        $form = $this->createForm(RatingType::class, $rating)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $rating = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rating);
            $entityManager->flush();

            return $this->redirect('/rating');
        }

        return $this->render('front/modifierRating.html.twig', [
            'rating' => $rating,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("rating/{nomRating}/supprimer", name="supprimerRating")
     */
    public function supprimerRating($nomRating)
    {
        $manager = $this->getDoctrine()->getManager();
        $rating = $manager->getRepository(Rating::class)->find($nomRating);
        $manager->remove($rating);
        $manager->flush();
        return $this->redirect('/rating');
    }
}

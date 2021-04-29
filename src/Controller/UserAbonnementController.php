<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\Abonnement2Type;
use App\Repository\AbonnementRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/abonnement")
 */
class UserAbonnementController extends AbstractController
{
    /**
     * @Route("/", name="user_abonnement_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator,Request $request): Response
    {
        $abonnements = $this->getDoctrine()
            ->getRepository(Abonnement::class)
            ->findAll();
        $abonnements = $paginator->paginate(
        // Doctrine Query, not results
            $abonnements,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('user_abonnement/index.html.twig', [
            'abonnements' => $abonnements,
        ]);
    }

    /**
     * @Route("/new", name="user_abonnement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $abonnement = new Abonnement();
        $abonnement->setDatecreation(new \DateTime('now'));
        $form = $this->createForm(Abonnement2Type::class, $abonnement);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('user_abonnement_index');
        }

        return $this->render('user_abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_abonnement_show", methods={"GET"})
     */
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('user_abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_abonnement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Abonnement $abonnement): Response
    {
        $form = $this->createForm(Abonnement2Type::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_abonnement_index');
        }

        return $this->render('user_abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_abonnement_delete", methods={"POST"})
     */
    public function delete(Request $request, Abonnement $abonnement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_abonnement_index');
    }

    /**
     * @Route("trierabonnements", name="trierabonnementsuser")
     */
    public function trierAbonnements(AbonnementRepository $abonnementRepository, Request $request)
    {
        $abonnements = $abonnementRepository->findAll();
        if (isset($_POST['trie'])) {
            if ($_POST['trie'] == 'moins') {
                $abonnements = $abonnementRepository->TriMin();
            }
            if ($_POST['trie'] == 'plus') {
                $abonnements = $abonnementRepository->TriMax();
            }
        }
        return $this->render('user_abonnement/index.html.twig',
            [
                'controller_name' => 'UserAbonnemmentController',
                'abonnements' => $abonnements,
            ]);
    }
}

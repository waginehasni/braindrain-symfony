<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\Abonnement1Type;
use App\Repository\AbonnementRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/abonnements")
 */

class AbonnementsController extends AbstractController
{
    /**
     * @Route("/", name="abonnements_index", methods={"GET"})
     */
    public function index(): Response
    {
        $abonnements = $this->getDoctrine()
            ->getRepository(Abonnement::class)
            ->findAll();

        return $this->render('abonnements/index.html.twig', [
            'abonnements' => $abonnements,
        ]);
    }


    /**
     * @Route("/new", name="abonnements_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $abonnement = new Abonnement();
        $abonnement->setDatecreation(new \DateTime('now'));
        $form = $this->createForm(Abonnement1Type::class, $abonnement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('abonnements_index');
        }

        return $this->render('abonnements/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="abonnements_show", methods={"GET"})
     */
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnements/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="abonnements_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Abonnement $abonnement): Response
    {
        $form = $this->createForm(Abonnement1Type::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('abonnements_index');
        }

        return $this->render('abonnements/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="abonnements_delete", methods={"POST"})
     */
    public function delete(Request $request, Abonnement $abonnement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('abonnements_index');
    }


    /**
     * @Route("trierabonnements", name="trierabonnements")
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
        return $this->render('abonnements/index.html.twig',
            [
                'controller_name' => 'AbonnemmentsController',
                'abonnements' => $abonnements,
            ]);
    }
    /**
     * @Route("/export/pdf", name="imprimerAbonnements", methods={"GET"})
     */
    public function pdf(AbonnementRepository $abonnementRepository): Response
    {

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('abonnements/pdf.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();


        $dompdf->stream("Abonnements.pdf", [
            "Attachment" => true
        ]);
    }

}

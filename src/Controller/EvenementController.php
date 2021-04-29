<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;

class EvenementController extends AbstractController
{

    /**
     * @Route("evenement")
     */
    public function index()
    {
        return $this->render('/front/afficherEvenement.html.twig', [
            'evenements' => $this->getDoctrine()->getRepository(Evenement::class)->findAll()
        ]);
    }

    /**
     * @Route("back/evenement")
     */
    public function indexb()
    {
        return $this->render('/back/afficherEvenement.html.twig', [
            'evenements' => $this->getDoctrine()->getRepository(Evenement::class)->findAll()
        ]);
    }

    /**
     * @Route("back/evenement/ajouter", name="ajouter_evenement")
     */
    public function ajouterEvenement(Request $request)
    {
        $evenement = new Evenement();

        $form = $this->createForm(EvenementType::class, $evenement)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenement = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirect('/back/evenement');
        }

        return $this->render('back/manipulerEvenement.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
            'manipulation' => 'ajouter',
        ]);
    }

    /**
     * @Route("back/evenement/{id}/modifier", name="modifier_evenement")
     */
    public function modifierEvenement(Request $request, $id)
    {

        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);

        $form = $this->createForm(EvenementType::class, $evenement)
            ->add('submit', SubmitType::class)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            $evenement = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirect('/back/evenement');
        }

        return $this->render('back/manipulerEvenement.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
            'manipulation' => 'Modifier',
        ]);
    }

    /**
     * @Route("back/evenement/{id}/supprimer", name="supprimer_evenement")
     */
    public function supprimerEvenement($id, \Swift_Mailer $mailer)
    {
        $manager = $this->getDoctrine()->getManager();
        $evenement = $manager->getRepository(Evenement::class)->find($id);
        $message = (new \Swift_Message('SUPPRESSION'))
            ->setFrom('wagine.hasni@esprit.tn')
            ->setTo('wagine.hasni@esprit.tn')
            ->setBody(
                $this->renderView(
                    'front/suppression_mail.html.twig'
                ),
                'text/html'
            );

        $manager->remove($evenement);
        $manager->flush();
        $mailer->send($message);

        return $this->redirect('/back/evenement');
    }

    /**
     * @Route("/evenement/recherche")
     * @throws Exception
     */
    public function rechercheCandidat(Request $request)
    {
        $recherche = $request->get('recherche');

        $evenements = $this->getDoctrine()->getRepository(Evenement::class)
            ->findOneByNom($recherche);

        $i = 0;
        $jsonContent = null;
        if ($evenements != null) {
            foreach ($evenements as $evenement) {
                $jsonContent[$i]["id"] = $evenement->getIdEvenement();
                $jsonContent[$i]["nom"] = $evenement->getNom();
                $jsonContent[$i]["nomOffre"] = $evenement->getNomOffre();
                $jsonContent[$i]["dateDebut"] = $evenement->getDateDebut()->format('d-m-Y');
                $jsonContent[$i]["dateFin"] = $evenement->getDateFin()->format('d-m-Y');
                $jsonContent[$i]["numSalle"] = $evenement->getNumSalle();
                $jsonContent[$i]["specialite"] = $evenement->getSpecialite();
                $i++;
            }
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("/TrierParnumsalle", name="TrierParnumsalle")
     */
    public function TrierParnumsalle(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Evenement::class);
        $evenement = $repository->findBynumSalle();

        return $this->render('back/afficherEvenement.html.twig', [
            'evenements' => $evenement,
        ]);
    }

    /**
     * @Route("/pagination", name="pagination")
     */
    public function pagination(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Appointments entity
        $appointmentsRepository = $em->getRepository(Evenement::class);

        // Find all the data on the Appointments table, filter your query as you need
        $allAppointmentsQuery = $appointmentsRepository->createQueryBuilder('p')
            ->where('p.nom != :nom')
            ->setParameter('nom', 'canceled')
            ->getQuery();

        // Paginate the results of the query
        $appointments = $paginator->paginate(
        // Doctrine Query, not results
            $allAppointmentsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            1
        );
        //dd($listEvenements);
        return $this->render('front/file.html.twig', [
            'controller_name' => 'EvenementController', 'evenements' => $appointments
        ]);
    }


}
<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation")
     */
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Appointments entity
        $appointmentsRepository = $em->getRepository(Reservation::class);

        // Find all the data on the Appointments table, filter your query as you need
        $allAppointmentsQuery = $appointmentsRepository->createQueryBuilder('p')
            ->where('p.numsalles != :numsalles')
            ->setParameter('numsalles', 'canceled')
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
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController', 'reservation' => $appointments
        ]);
    }
    /**
     * @Route("/listpp", name="reservationPDF" )
     */
    public function listpp(ReservationRepository $ReservationRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $reservation= $ReservationRepository->findAll();





        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reservation/listpp.html.twig', [
            'reservation' => $reservation,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request)
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->add('ajouter', SubmitType::class, ['label' => 'ajouter']);
        //on a créé notre formulaire et on lui a passé en argument notre objet
        $form->handleRequest($request);
        //le formulaire traite la requete reçue

        //if les données reçues sont valides alors on va faire persist
        if (($form->isSubmitted()) && ($form->isValid())) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation); //l'ajout dans la base
            ////persist joue le role de insert into
            $em->flush();
            return $this->redirectToRoute('read');
        } else //le cas où les données sont invalides ou ne sont pas soumis
        {
            return $this->render('reservation/create.html.twig', [
                'controller_name' => 'ReservationController',
                'form' => $form->createView() //envoyé vers le twig une vue de notre formulaire
            ]);

        }
    }

    /**
     * @Route("/read", name="read")
     */
    public function read()
    {
        $listReservation = $this->getDoctrine()->getRepository(Reservation::class)->findAll();
        //dd($listReservation);
        return $this->render('reservation/read.html.twig', [
            'controller_name' => 'ReservationController', 'reservation' => $listReservation

        ]);
    }

    /**
     * @Route("/delete/{numreservation}", name="delete")
     */
    public function delete($numreservation)
    {
        $Reservation = $this->getDoctrine()->
        getRepository(Reservation::class)
            ->find($numreservation);
        //dd($listClassroom);
        $em = $this->getDoctrine()->getManager();
        $em->remove($Reservation);
        $em->flush();
        return $this->redirectToRoute('read');

    }

    /**
     * @Route("/update/{numreservation}", name="update")
     */
    public function update(Request $request, $numreservation)
    {//1ere etape : chercher l'objet à modifier
        $reservation = $this->getDoctrine()
            ->getRepository(Reservation::class)->find($numreservation);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->add('modifier', SubmitType::class, ['label' => 'modifier']);
        //ona créé notre formulaire et on lui a passé en argument notre objet
        $form->handleRequest($request);
        //le formulaire traite la requete reçue

        //if les données reçues sont valides alors on va faire persiste
        if (($form->isSubmitted()) && ($form->isValid())) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('read');
        } else //le cas où les données sont invalides oun ne sont pas soumis
        {
            return $this->render('reservation/modify.html.twig', [
                'controller_name' => 'ReservationController',
                'form' => $form->createView() //envoyé vers le twig
            ]);

        }
    }
}

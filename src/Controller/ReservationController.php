<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\BarChart;
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
     * @Route("/listp4", name="reservationPDF" )
     */
    public function listp4(ReservationRepository $ReservationRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $reservation= $ReservationRepository->findAll();





        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reservation/listp4.html.twig', [
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
     * @Route("/create4", name="create4")
     */
    public function create4(Request $request ,\Swift_Mailer $mailer):Response
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
            $message = (new \Swift_Message('Confirmation ajout reservation'))
                ->setFrom('sarra.hamzaoui@esprit.tn')
                ->setTo('hadil.chahba@esprit.tn')
                ->setBody(
                    $this->renderView(
                        'reservation/email2.html.twig', ["reservation"=>$reservation]
                    ),


                    'text/html'
                );
            $mailer->send($message);
            $em->persist($reservation); //l'ajout dans la base
            ////persist joue le role de insert into
            $em->flush();
            $this->addFlash(
                'info',
                'added successfully!'
            );
            return $this->redirectToRoute('read4');
        } else //le cas où les données sont invalides ou ne sont pas soumis
        {
            return $this->render('reservation/create4.html.twig', [
                'controller_name' => 'ReservationController',
                'form' => $form->createView() //envoyé vers le twig une vue de notre formulaire
            ]);

        }
    }

    /**
     * @Route("/read4", name="read4")
     */
    public function read4()
    {
        $listReservation = $this->getDoctrine()->getRepository(Reservation::class)->findAll();
        //dd($listReservation);
        return $this->render('reservation/read4.html.twig', [
            'controller_name' => 'ReservationController', 'reservation' => $listReservation

        ]);
    }

    /**
     * @Route("/delete4/{numreservation}", name="delete4")
     */
    public function delete4($numreservation)
    {
        $Reservation = $this->getDoctrine()->
        getRepository(Reservation::class)
            ->find($numreservation);
        //dd($listClassroom);
        $em = $this->getDoctrine()->getManager();
        $em->remove($Reservation);
        $em->flush();
        return $this->redirectToRoute('read4');

    }

    /**
     * @Route("/update4/{numreservation}", name="update4")
     */
    public function update4(Request $request, $numreservation)
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
            return $this->redirectToRoute('read4');
        } else //le cas où les données sont invalides oun ne sont pas soumis
        {
            return $this->render('reservation/modify4.html.twig', [
                'controller_name' => 'ReservationController',
                'form' => $form->createView() //envoyé vers le twig
            ]);

        }
    }
    /**
     * @Route("/stats", name="statsreservation")
     */
    public function stats() : Response
    {

        $p=$this->getDoctrine()->getRepository(Reservation::class);
        //year
        $years = $p->getYear();
        $data = [['Years', 'Nombre de reservations']];
        foreach($years as $year)
        {
            $data[] = array($year['year'], $year['post']);
        }

        $bar1 = new barchart();
        $bar1->getData()->setArrayToDataTable(
            $data
        );
        $bar1->getOptions()->setTitle('par années');
        $bar1->getOptions()->getTitleTextStyle()->setColor('#07600');
        $bar1->getOptions()->getTitleTextStyle()->setFontSize(25);

        //month
        $months = $p->getMonth();
        $data = [['Mois', 'Nombre de reservations']];
        foreach($months as $month)
        {
            $data[] = array($month['month'], $month['post']);
        }

        $bar2 = new barchart();
        $bar2->getData()->setArrayToDataTable(
            $data
        );
        $bar2->getOptions()->setTitle('par mois');
        $bar2->getOptions()->getTitleTextStyle()->setColor('#07600');
        $bar2->getOptions()->getTitleTextStyle()->setFontSize(25);

        //day
        $days = $p->getDay();
        $data = [['Années', 'Nombre de reservations']];
        foreach($days as $day)
        {
            $data[] = array($day['day'], $day['post']);
        }

        $bar3 = new barchart();
        $bar3->getData()->setArrayToDataTable(
            $data
        );
        $bar3->getOptions()->setTitle('par jour');
        $bar3->getOptions()->getTitleTextStyle()->setColor('#07600');
        $bar3->getOptions()->getTitleTextStyle()->setFontSize(25);


        return $this->render('reservation/stats.html.twig', array('barchart1' => $bar1, 'barchart2' => $bar2,'barchart3' => $bar3));
    }
}

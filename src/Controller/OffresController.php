<?php

namespace App\Controller;

use App\Entity\Offres;
use App\Form\OffresType;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OffresRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twilio\Rest\Client;



class OffresController extends AbstractController
{
    /**
     * @Route("/offres", name="offres")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Appointments entity
        $appointmentsRepository = $em->getRepository( Offres::class);

        // Find all the data on the Appointments table, filter your query as you need
        $allAppointmentsQuery = $appointmentsRepository->createQueryBuilder('p')
            ->where('p.type != :type')
            ->setParameter('type', 'canceled')
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
        return $this->render('offres/index.html.twig', [
            'controller_name' => 'OffresController', 'offres' => $appointments
        ]);
    }





    /**
     * @Route("/listp", name="offresPDF" )
     */
    public function listp(OffresRepository $offresRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $offres= $offresRepository->findAll();





        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('offres/listp.html.twig', [
            'offres' => $offres,
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
    public function create(Request $request ,\Swift_Mailer $mailer)
    {
        $offres= new Offres();
        $form=$this->createForm(OffresType::class,$offres);
        $form-> add('ajouter',SubmitType::class,['label'=>'ajouter']);
        //on a créé notre formulaire et on lui a passé en argument notre objet
        $form->handleRequest($request);
        //le formulaire traite la requete reçue

        //if les données reçues sont valides alors on va faire persist
        if(($form->isSubmitted())&&($form->isValid()))
        {$em=$this->getDoctrine()->getManager();
            $message = (new \Swift_Message('AJOUT'))
                ->setFrom('sarra.hamzaoui@esprit.tn')
                ->setTo('sarra.hamzaoui@esprit.tn')
                ->setBody(
                    $this->renderView(
                        'offres/ajout_mail.html.twig'
                    ),
                    'text/html'
                );

            $em->persist($offres); //l'ajout dans la base
            ////persist joue le role de insert into
            $em->flush();

            $this->addFlash(
              'info',
               'added successfully!'
            );
            $mailer->send($message);
            $sid    = "AC305f7800454921eaf7bbeb75a59603b5";
            $token  = "3a1d25a6b6aca12022fa77661750df54";
            $twilio = new Client($sid, $token);


            $message = $twilio->messages
                ->create("+21658936313",
                    array(
                        "body" => "offre ajouté",
                        "from" => "+16162059921"
                    )
                );

            return $this->redirectToRoute('read');








        }

        else //le cas où les données sont invalides ou ne sont pas soumis
        {
            return $this->render('offres/create.html.twig', [
                'controller_name' => 'OffresController',
                'form'=> $form ->createView() //envoyé vers le twig une vue de notre formulaire
            ]);

        }




    }


    /**
     * @Route("/read", name="read")
     */
    public function read()
    {
        $listOffres=$this->getDoctrine()->getRepository(Offres::class)->findAll();
        //dd($listoffres);
        return $this->render('offres/read.html.twig', [
            'controller_name' => 'OffresController','offres'=>$listOffres
        ]);
    }




    /**
     * @Route("/delete/{idoffre}", name="delete")
     */
    public function delete($idoffre)
    {
        $offres=$this->getDoctrine()->
        getRepository(Offres::class)
            ->find($idoffre);
        //dd($listClassroom);
        $em=$this->getDoctrine()->getManager();
        $em->remove($offres);
        $em->flush();
        return $this->redirectToRoute('read');

    }


    /**
     * @Route("/update/{idoffre}", name="update")
     */
    public function update(Request $request,$idoffre)
    {//1ere etape : chercher l'objet à modifier
        $offres = $this->getDoctrine()
            ->getRepository(Offres::class)->find($idoffre);
        $form = $this->createForm(OffresType::class, $offres);
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
            return $this->render('offres/modify.html.twig', [
                'controller_name' => 'OffresController',
                'form' => $form->createView() //envoyé vers le twig
            ]);

        }}









}

<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class CoursController extends AbstractController
{
    /**
     * @Route("/cours", name="cours")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $em = $this->getDoctrine()->getManager();

        // Get some repository of data, in our case we have an Appointments entity
        $appointmentsRepository = $em->getRepository(Cours::class);

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
        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController', 'cours' => $appointments
        ]);
    }
    /**
     * @Route("/listp", name="coursPDF" )
     */
    public function listp(CoursRepository $CoursRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $cours= $CoursRepository->findAll();





        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('cours/listp.html.twig', [
            'cours' => $cours,
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
     * @Route("/ajout", name="ajout")
     */
    public function ajout(Request $request , \Swift_Mailer $mailer)
    {
        $cours = new Cours();
        $form = $this->createForm(CoursType::class, $cours);
        $form->add('ajouter', SubmitType::class, ['label' => 'ajouter']);
        //on a créé notre formulaire et on lui a passé en argument notre objet
        $form->handleRequest($request);
        //le formulaire traite la requete reçue

        //if les données reçues sont valides alors on va faire persist
        if (($form->isSubmitted()) && ($form->isValid())) {
            $em = $this->getDoctrine()->getManager();
            $message = (new \Swift_Message('Cours ajouté'))
                ->setFrom('hadil.chahba@esprit.tn')
                ->setTo('hadil.chahba@esprit.tn')
                ->setBody(
                    $this->renderView(
                        'cours/ajoutermail.html.twig'
                    ),
                    'text/html'
                );

            $em->persist($cours); //l'ajout dans la base
            ////persist joue le role de insert into
            $em->flush();
            $this->addFlash(
                'info',
                'added successfully!'
            );
            $mailer->send($message);
            return $this->redirectToRoute('read2');
        } else //le cas où les données sont invalides ou ne sont pas soumis
        {
            return $this->render('cours/ajout.html.twig', [
                'controller_name' => 'CoursController',
                'form' => $form->createView() //envoyé vers le twig une vue de notre formulaire
            ]);

        }
    }

    /**
     * @Route("/read2", name="read2")
     */
    public function read2()
    {
        $listCours = $this->getDoctrine()->getRepository(Cours::class)->findAll();
        //dd($listCours);
        return $this->render('cours/read2.html.twig', [
            'controller_name' => 'CoursController', 'cours' => $listCours

        ]);
    }

    /**
     * @Route("/delete2/{numcours}", name="delete2")
     */
    public function delete2($numcours)
    {
        $Cours = $this->getDoctrine()->
        getRepository(Cours::class)
            ->find($numcours);
        //dd($listClassroom);
        $em = $this->getDoctrine()->getManager();
        $em->remove($Cours);
        $em->flush();
        return $this->redirectToRoute('read2');

    }

    /**
     * @Route("/update2/{numcours}", name="update2")
     */
    public function update2(Request $request, $numcours)
    {//1ere etape : chercher l'objet à modifier
        $cours = $this->getDoctrine()
            ->getRepository(Cours::class)->find($numcours);
        $form = $this->createForm(CoursType::class, $cours);
        $form->add('modifier', SubmitType::class, ['label' => 'modifier']);
        //ona créé notre formulaire et on lui a passé en argument notre objet
        $form->handleRequest($request);
        //le formulaire traite la requete reçue

        //if les données reçues sont valides alors on va faire persiste
        if (($form->isSubmitted()) && ($form->isValid())) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('read2');
        } else //le cas où les données sont invalides oun ne sont pas soumis
        {
            return $this->render('cours/modify2.html.twig', [
                'controller_name' => 'CoursController',
                'form' => $form->createView() //envoyé vers le twig
            ]);

        }
    }


    /**
     * @Route("/TrierParPrix", name="TrierParPrix")
     */
    public function TrierParPrix(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Cours::class);
        $cours = $repository->findByPrix();

        return $this->render('cours/read2.html.twig', [
            'cours' => $cours,
        ]);
    }



    /**
     * @Route("/Excel", name="Excel")
     */
    public function Excel()
    {
        $cours = $this->getDoctrine()->getRepository(Cours::class)->findAll();
        $phpExcelObject = new Spreadsheet();


        $phpExcelObject->getActiveSheet()
            ->setCellValue('A1' , $cours->getNumreservation())
            ->setCellValue('E1' , $cours->getNomcours())
            ->setCellValue('F1' , $cours->getNomcoach())
            ->setCellValue('G1', $cours->getType())
            ->setCellValue('H1' , $cours->getPrix());




        $writer = new Xlsx( $phpExcelObject);

        // Create a Temporary file in the system
        $fileName = 'CoursExcel.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        $writer->save($temp_file);

        // Return the excel file as an attachment
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }

}

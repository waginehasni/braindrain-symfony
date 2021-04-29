<?php

namespace App\Controller;

use App\Entity\Sponsors;
use App\Form\SponsorsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SponsorsRepository;
use Dompdf\Dompdf;
use Dompdf\Options;


class SponsorsController extends AbstractController
{
    /**
     * @Route("/sponsors", name="sponsors")
     */
    public function index(): Response
    {
        return $this->render('sponsors/index.html.twig', [
            'controller_name' => 'SponsorsController',
        ]);
    }

    /**
     * @Route("/listpp", name="sponsorsPDF" )
     */
    public function listp(SponsorsRepository $sponsorsRepository): Response
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $sponsors= $sponsorsRepository->findAll();





        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('sponsors/listpp.html.twig', [
            'sponsors' => $sponsors,
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
     * @Route("/create2", name="create2")
     */
    public function create2(Request $request ,\Swift_Mailer $mailer):Response
    {
        $sponsors= new Sponsors();
        $form=$this->createForm(SponsorsType::class,$sponsors);
        $form-> add('ajouter',SubmitType::class,['label'=>'ajouter']);
        //on a créé notre formulaire et on lui a passé en argument notre objet
        $form->handleRequest($request);
        //le formulaire traite la requete reçue

        //if les données reçues sont valides alors on va faire persist
        if(($form->isSubmitted())&&($form->isValid()))
        {$em=$this->getDoctrine()->getManager();
            $message = (new \Swift_Message('Confirmation ajout sponsor'))
                ->setFrom('sarra.hamzaoui@esprit.tn')
                ->setTo('sarra.hamzaoui@esprit.tn')
                ->setBody(
                    $this->renderView(
                        'sponsors/email.html.twig', ["sponsors"=>$sponsors]
                    ),


                    'text/html'
                );
            $mailer->send($message);

            $em->persist($sponsors); //l'ajout dans la base
            ////persist joue le role de insert into
            $em->flush();
            $this->addFlash(
                'info',
                'added successfully!'
            );
            $mailer->send($message);
            return $this->redirectToRoute('read2');
        }
        else //le cas où les données sont invalides ou ne sont pas soumis
        {
            return $this->render('sponsors/create2.html.twig', [
                'controller_name' => 'SponsorsController',
                'form'=> $form ->createView() //envoyé vers le twig une vue de notre formulaire
            ]);

        }
    }


    /**
     * @Route("/read2", name="read2")
     */
    public function read2()
    {
        $listSponsors=$this->getDoctrine()->getRepository(Sponsors::class)->findAll();
        //dd($listsponsors);
        return $this->render('sponsors/read2.html.twig', [
            'controller_name' => 'SponsorsController','sponsors'=>$listSponsors
        ]);
    }




    /**
     * @Route("/delete2/{idsponsor}", name="delete2")
     */
    public function delete2($idsponsor)
    {
        $sponsors=$this->getDoctrine()->
        getRepository(Sponsors::class)
            ->find($idsponsor);
        //dd($listClassroom);
        $em=$this->getDoctrine()->getManager();
        $em->remove($sponsors);
        $em->flush();
        return $this->redirectToRoute('read2');

    }


    /**
     * @Route("/update2/{idsponsor}", name="update2")
     */
    public function update2(Request $request,$idsponsor)
    {//1ere etape : chercher l'objet à modifier
        $sponsors = $this->getDoctrine()
            ->getRepository(Sponsors::class)->find($idsponsor);
        $form = $this->createForm(SponsorsType::class, $sponsors);
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
            return $this->render('sponsors/modify2.html.twig', [
                'controller_name' => 'SponsorsController',
                'form' => $form->createView() //envoyé vers le twig
            ]);

        }


    }

    /**
     * @Route("/TrierParBudget", name="TrierParBudget")
     */
    public function TrierParBudget(Request $request): Response
    {
        $repository = $this->getDoctrine()->getRepository(Sponsors::class);
        $sponsors = $repository->findByBudget();

        return $this->render('sponsors/read2.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }







}

<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Form\Reclamation1Type;
use App\Form\Reclamation2Type;
use App\Repository\ReclamationRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reclamation")
 */
class ReclamationController extends AbstractController
{

    /**
     * @var ReclamationRepository
     */
    private $repository;

    public function __construct(ReclamationRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @Route("/cloture", name="reclamation_index_cloture", methods={"GET"})
     */
    public function indexCloture(Request $request): Response
    {
        $reclamations = $this->getDoctrine()
            ->getRepository(Reclamation::class)
            ->findRecByEtat("cloturé");
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    /**
     * @Route("/", name="reclamation_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $reclamations = $this->getDoctrine()
            ->getRepository(Reclamation::class)
            ->findRecByEtat("actif");

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    function filterwords($text){
        $delimiter = ',';
        $enclosure = '"';
        $header = NULL;
        $data = array();

        if (($handle = fopen("https://docs.google.com/spreadsheets/d/10P3ihV-l2Hz9Jm1Cprp8S7mTKqYsOZWxzaNOC8ij72M/export?format=csv", 'r')) !== FALSE) {

            while (($row = fgetcsv($handle, 0, $delimiter, $enclosure)) !== FALSE) {

                if(!$header) {
                    $header = $row;
                } else {
                    array_push($data,$row);
                }
            }
            fclose($handle);
        }
        #dd($data[300][0]);
        $filterWords = array('badword');
        foreach($data as $s)
        {
            array_push($filterWords,$s[0]);
        }
        #dd($filterWords);
        $filterCount = sizeof($filterWords);
        for ($i = 0; $i < $filterCount; $i++) {
            $text = preg_replace_callback('/\b' . $filterWords[$i] . '\b/i', function($matches){return str_repeat('*', strlen($matches[0]));}, $text);
        }
        return $text;
    }

    /**
     * @Route("/new", name="reclamation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(Reclamation1Type::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $badWords = $this->filterwords($reclamation->getTitre()." ".$reclamation->getDescription());
            if (strpos($badWords, '**') !== false){
                $this->addFlash('info', 'Enlevez les Gros mots SVP !');
            }else{
                if($form->get('image')->getData()!=null) {
                    $file = $form->get('image')->getData();

                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $reclamation->setImage($filename);
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            $this->getParameter('brochures_directory'),
                            $filename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload

                    }
                }
                $reclamation->setEtat("actif");
                $reclamation->setDatereclamation(new \DateTime('now'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($reclamation);
                $entityManager->flush();

                return $this->redirectToRoute('reclamation_index');
            }

        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reclamation_show", methods={"GET"})
     */
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reclamation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reclamation $reclamation): Response
    {
        $form = $this->createForm(Reclamation2Type::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $badWords = $this->filterwords($reclamation->getReponse());
            if (strpos($badWords, '**') !== false){
                $this->addFlash('info', 'Enlevez les Gros mots SVP !');
            }else{
                if($form->get('image')->getData()!=null) {
                    $file = $form->get('image')->getData();

                    $filename = md5(uniqid()) . '.' . $file->guessExtension();
                    $reclamation->setImage($filename);
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            $this->getParameter('brochures_directory'),
                            $filename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload

                    }
                }
                $reclamation->setEtat("cloturé");
                $reclamation->setDatereponse(new \DateTime('now'));
                $reclamation->setSatisfaction("satisfait");
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('reclamation_index');
            }

        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reclamation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reclamation $reclamation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reclamation_index');
    }

    /**
     * @Route("trier", name="trier")
     */
    public function trier(ReclamationRepository $reclamationRepository, Request $request )
    {
        $reclamation = $reclamationRepository->findAll();
        if (isset($_POST['trie'])) {
            if ($_POST['trie'] == 'moins') {
                $reclamation = $reclamationRepository->TriMin();
            }
            if ($_POST['trie'] == 'plus') {
                $reclamation = $reclamationRepository->TriMax();
            }
        }

        return $this->render('reclamation/index.html.twig',
            [
                'controller_name' => 'ReclamationController',
                'reclamations' => $reclamation,
            ]);
    }


    /**
     * @Route("searchrec",name="searchrec")
     */
    public function search(Request $request)
    {
        $requestString = $request->get('q');
        $posts = $this->repository->findByString($requestString);
        if (!$posts) {
            $result['posts']['error'] = 'Aucun  trouvé';
        } else {
            $result['posts'] = $this->getRealEntities($posts);
        }
        return new Response(json_encode($result));
    }

    public function getRealEntities($posts)
    {
        foreach ($posts as $post) {
            $realEntities[$post->getTitre()] = [$post->getTitre(),$post->getId()];
        }
        return $realEntities;
    }

    /**
     * @Route("/export/pdf", name="imprimer", methods={"GET"})
     */
    public function pdf(ReclamationRepository $reclamationRepository): Response
    {

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView('reclamation/pdf.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();


        $dompdf->stream("Reclamations.pdf", [
            "Attachment" => true
        ]);
    }


    /**
     * @Route("/export/stat", name="stat", methods={"GET"})
     */
    public function Statistic(ReclamationRepository  $reclamationRepository ,Request $request): Response
    {
        $reclamation = $reclamationRepository->findAll();
        $catType= ['Cloturé', 'En cours']; #, 'Suivi', 'Msg', 'Technical', 'Posts'];
        $catColor = ['#36CAAB', '#B370CF']; #, '#34495E', '#B370CF', '#AC5353', '#CFD4D8'];
        $catDone= count($reclamationRepository->findBy(["etat" =>"cloture"]) )  ;
        $catToDo = count($reclamationRepository->findBy(["etat" =>"actif"]) ) ;
        #$catSuivi = count($reclamationRepository->findBy(["idCat" => "Suivi"]) ) ;
        #$catMsg= count($reclamationRepository->findBy(["idCat" =>"Msg"]) )  ;
        #$catTechnical = count($reclamationRepository->findBy(["idCat" =>"Technical"]) ) ;
        #$catPosts = count($reclamationRepository->findBy(["idCat" => "Posts"]) ) ;
        $catCount = [ $catDone, $catToDo]; #,$categSuivi, $categMsg, $categTechnical, $categPosts];

        return $this->render('reclamation/statistics.html.twig',
            ['catType' => json_encode($catType),
                'catColor' => json_encode($catColor),
                'catCount' => json_encode($catCount),


            ]);
        # return $this->render('reclamation/statistics.html.twig');
    }
    /**
     * @Route("/export/stat/satisfaction", name="statSatisfaction", methods={"GET"})
     */
    public function satisfactionStatistic(ReclamationRepository  $reclamationRepository ,Request $request): Response
    {

        $catType= ['Satisfaits', 'non Satisfaits'];
        $catColor = ['#36CAAB', '#FF0000    '];
        $catDone= count($reclamationRepository->findBy(["satisfaction" =>"satisfait"]) )  ;
        $catToDo = count($reclamationRepository->findBy(["satisfaction" =>"non satisfait"]) ) ;
        $catCount = [ $catDone, $catToDo];

        return $this->render('reclamation/statisticsSatisfaction.html.twig',
            ['catType' => json_encode($catType),
                'catColor' => json_encode($catColor),
                'catCount' => json_encode($catCount),


            ]);
    }


}

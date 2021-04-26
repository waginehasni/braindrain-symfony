<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Form\Reclamation1Type;
use App\Form\Reclamation2Type;
use App\Repository\ReclamationRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function indexCloture(PaginatorInterface $paginator, Request $request): Response
    {
        $reclamations = $this->getDoctrine()
            ->getRepository(Reclamation::class)
            ->findRecByEtat("cloturé");

        $reclamations = $paginator->paginate(
        // Doctrine Query, not results
            $reclamations,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    /**
     * @Route("/", name="reclamation_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $reclamations = $this->getDoctrine()
            ->getRepository(Reclamation::class)
            ->findRecByEtat("actif");

        $reclamations = $paginator->paginate(
        // Doctrine Query, not results
            $reclamations,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    function filterwords($text){
        $filterWords = array('fuck','pute','bitch');
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
    public function trier(ReclamationRepository $reclamationRepository, Request $request,PaginatorInterface $paginator )
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
        $reclamation = $paginator->paginate(
        // Doctrine Query, not results
            $reclamation,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );
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


}

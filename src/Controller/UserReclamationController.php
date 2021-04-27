<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\Reclamation1Type;
use App\Form\Reclamation2Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/reclamation")
 */
class UserReclamationController extends AbstractController
{
    /**
     * @Route("/", name="user_reclamation_index", methods={"GET"})
     */
    public function index(): Response
    {
        $reclamations = $this->getDoctrine()
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('user_reclamation/index.html.twig', [
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
     * @Route("/new", name="user_reclamation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(Reclamation1Type::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $badWords = $this->filterwords($reclamation->getDescription().' '.$reclamation->getTitre());
            if (strpos($badWords, '**') !== false){
                $this->addFlash('info', 'Enlevez les Gros mots SVP !');
            }
            else
            {
                $reclamation->setEtat("actif");
                $reclamation->setDatereponse(new \DateTime('now'));
                if($form->get('image')->getData()!=null){
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();
            return $this->redirectToRoute('user_reclamation_index');
            }
        }

        return $this->render('user_reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_reclamation_show", methods={"GET"})
     */
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('user_reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_reclamation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reclamation $reclamation): Response
    {
        $form = $this->createForm(Reclamation1Type::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $badWords = $this->filterwords($reclamation->getDescription().' '.$reclamation->getTitre());
            if (strpos($badWords, '**') !== false){
                $this->addFlash('info', 'Enlevez les Gros mots SVP !');
            }else {
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
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('user_reclamation_index');
            }

        }

        return $this->render('user_reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_reclamation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reclamation $reclamation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_reclamation_index');
    }

}

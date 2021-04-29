<?php

namespace App\Controller;

use App\Entity\FosUser;
use App\Form\FosUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/fos/user")
 */
class FosUserController extends AbstractController
{
    /**
     * @Route("/", name="fos_user_index", methods={"GET"})
     */
    public function index(): Response
    {
        $fosUsers = $this->getDoctrine()
            ->getRepository(FosUser::class)
            ->findAll();

        return $this->render('back/utilisateur/index.html.twig', [
            'fos_users' => $fosUsers,
        ]);
    }

    /**
     * @Route("/new", name="fos_user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $fosUser = new FosUser();
        $form = $this->createForm(FosUserType::class, $fosUser)
            ->add("submit", SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fosUser);
            $entityManager->flush();

            return $this->redirectToRoute('fos_user_index');
        }

        return $this->render('back/utilisateur/new.html.twig', [
            'fos_user' => $fosUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="fos_user_show", methods={"GET","POST"})
     */
    public function show(FosUser $fosUser): Response
    {

        return $this->render('back/utilisateur/show.html.twig', [
            'fos_user' => $fosUser,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="fos_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FosUser $fosUser): Response
    {
        $form = $this->createForm(FosUserType::class, $fosUser)
            ->add("submit", SubmitType::class);;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('fos_user_index');
        }

        return $this->render('back/utilisateur/edit.html.twig', [
            'fos_user' => $fosUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/supprimer", name="fos_user_delete", methods={"POST"})
     */
    public function delete(Request $request, $id, \Swift_Mailer $mailer): Response
    {
        $fosUser = $this->getDoctrine()->getRepository(FosUser::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($fosUser);
        $entityManager->flush();


        return $this->redirectToRoute('fos_user_index');
    }
}

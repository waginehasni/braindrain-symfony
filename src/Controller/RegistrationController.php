<?php

namespace App\Controller;

use App\Entity\FosUser;
use App\Form\FosUserType;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register")
     * @throws Exception
     */
    public function registration(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new FosUser();
        $user->setRoles(["ROLE_ADMIN"]);

        $form = $this->createForm(RegistrationFormType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            // encode the plain password
            $user
                ->setPassword($passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('wagine.hasni@esprit.tn', 'sportify'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirect('login');
        }

        $this->addFlash('success', 'Veuillez verifier votre email.');

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('login');
    }
}
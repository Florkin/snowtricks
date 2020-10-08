<?php

namespace App\Controller\Manage;

use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class ManageUserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ManageUserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/manage/user/{id}-{slug}", name="manage.user.update", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     */
    public function update(int $id,
                           string $slug,
                           Request $request,
                           UserPasswordEncoderInterface $passwordEncoder,
                           GuardAuthenticatorHandler $guardHandler,
                           LoginFormAuthenticator $authenticator
    )
    {
        $user = $this->userRepository->find($id);
        $userSlug = $user->getSlug();

        if ($userSlug !== $slug) {
            return $this->redirectToRoute("user.show", [
                "id" => $user->getId(),
                "slug" => $userSlug
            ], 301);
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'current_menu' => 'app_register'
        ]);
    }
}

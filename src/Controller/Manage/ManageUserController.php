<?php

namespace App\Controller\Manage;

use App\Form\PasswordFormType;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var GuardAuthenticatorHandler
     */
    private $guardHandler;
    /**
     * @var LoginFormAuthenticator
     */
    private $authenticator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ManageUserController constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator)
    {
        $this->userRepository = $userRepository;
        $this->guardHandler = $guardHandler;
        $this->authenticator = $authenticator;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/manage/user/{id}-{slug}", name="manage.user.update", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @return Response
     */
    public function update(int $id, string $slug, Request $request)
    {
        $user = $this->userRepository->find($id);
        $this->denyAccessUnlessGranted('EDIT_USER', $user);

        $userSlug = $user->getSlug();

        if ($userSlug !== $slug) {
            return $this->redirectToRoute("manage.user.update", [
                "id" => $user->getId(),
                "slug" => $userSlug
            ], 301);
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            // do anything else you need here, like send an email

            $this->addFlash("success", "Votre profil a bien été modifié");
            return $this->guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('manage/user/form.html.twig', [
            "user" => $user,
            "userForm" => $form->createView(),
            "current_menu" => "app_user_modify"
        ]);
    }

    /**
     * @Route("/manage/user/password/{id}-{slug}", name="manage.password.update", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function passwordUpdate(int $id, string $slug, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->userRepository->find($id);
        $this->denyAccessUnlessGranted('EDIT_PASSWORD', $user);

        $userSlug = $user->getSlug();

        if ($userSlug !== $slug) {
            return $this->redirectToRoute("manage.password.update", [
                "id" => $user->getId(),
                "slug" => $userSlug
            ], 301);
        }

        $form = $this->createForm(PasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPwd = $form->get("old_password")->getData();
            $newPwd = $form->get("new_password")->getData();
            $newPwdConfirm = $form->get("new_password_confirm")->getData();

            if ($passwordEncoder->isPasswordValid($user, $oldPwd) && $newPwd == $newPwdConfirm) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $newPwd
                    )
                );

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash("success", "Le mot de passe a bien été modifié");
//                return $this->redirectToRoute("user.show");
            }
            return $this->guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->authenticator,
                'main'
            );

            if (!$passwordEncoder->isPasswordValid($user, $oldPwd)) {
                $this->addFlash("error", "L'ancien mot de passe n'est pas valide");
            }

            if ($newPwd != $newPwdConfirm) {
                $this->addFlash("error", "Les mot de passe ne sont pas identiques");
            }

            return $this->redirectToRoute("manage.password.update", ["id" => $user->getId(), "slug" => $userSlug]);
        }

        return $this->render('manage/user/password-form.html.twig', [
            'passwordForm' => $form->createView(),
            'current_menu' => 'app_user_modify'
        ]);
    }
}

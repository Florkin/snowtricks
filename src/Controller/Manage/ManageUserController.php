<?php

namespace App\Controller\Manage;

use App\Form\Model\ChangePassword;
use App\Form\PasswordFormType;
use App\Form\UserFormType;
use App\Handlers\Forms\PasswordFormHandler;
use App\Handlers\Forms\UserFormHandler;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @var UserFormHandler
     */
    private $formHandler;

    /**
     * ManageUserController constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserFormHandler $formHandler
     * @param UserRepository $userRepository
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     */
    public function __construct(EntityManagerInterface $entityManager, UserFormHandler $formHandler, UserRepository $userRepository, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator)
    {
        $this->userRepository = $userRepository;
        $this->guardHandler = $guardHandler;
        $this->authenticator = $authenticator;
        $this->entityManager = $entityManager;
        $this->formHandler = $formHandler;
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

        if ($this->formHandler->handle($request, $user, UserFormType::class)) {
            $this->addFlash("success", "Votre compte a bien été modifié");
            return $this->guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('manage/user/form.html.twig', [
            "user" => $user,
            "userForm" => $this->formHandler->createView(),
            "current_menu" => "app_user_modify"
        ]);
    }

    /**
     * @Route("/manage/user/password/{id}-{slug}", name="manage.password.update", requirements={"slug": "[a-z0-9\-]*", "id": "[0-9]*"})
     * @param int $id
     * @param string $slug
     * @param Request $request
     * @param PasswordFormHandler $formHandler
     * @return Response
     */
    public function passwordUpdate(int $id, string $slug, Request $request, PasswordFormHandler $formHandler)
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

        $changePassword = new ChangePassword();
        $formHandler->setUser($user);
        if ($formHandler->handle($request, $changePassword, PasswordFormType::class)) {
            $this->addFlash("success", "Le mot de passe a bien été modifié");
            return $this->guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('manage/user/password-form.html.twig', [
            'passwordForm' => $formHandler->createView(),
            'current_menu' => 'app_user_modify'
        ]);
    }
}

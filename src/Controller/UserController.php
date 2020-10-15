<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user.show")
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function show()
    {
        $user = $this->getUser();
        $roleDisplay = "Utilisateur";
        if ($this->isGranted('ROLE_ADMIN')) {
            $roleDisplay = "Admin";
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            "role_display" => $roleDisplay,
            "current_menu" => "user.show",
        ]);
    }
}

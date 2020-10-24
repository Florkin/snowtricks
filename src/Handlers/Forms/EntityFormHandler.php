<?php

namespace App\Handlers\Forms;

use App\Handlers\Forms\AbstractFormHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class EntityFormHandler extends AbstractFormHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var string
     */
    private $formType;

    /**
     * NewTrickFormHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface $flashBag
     */
    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }


    public function getFormType(): string
    {
        return $this->formType;
    }

    public function process($trick): void
    {
        $this->entityManager->persist($trick);
        $this->entityManager->flush();
        $this->flashBag->add("success", "Le trick " . $trick->getTitle() . " a bien été ajouté");
    }

    public function setFormType(string $formType): void
    {
        $this->formType = $formType;
    }


}
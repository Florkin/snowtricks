<?php

namespace App\DataFixtures;

use App\Entity\ChatPost;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ChatPostFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @var TrickRepository
     */
    private $trickRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * ChatPostFixture constructor.
     * @param TrickRepository $trickRepository
     * @param UserRepository $userRepository
     */
    public function __construct(TrickRepository $trickRepository, UserRepository $userRepository)
    {

        $this->trickRepository = $trickRepository;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $tricks = $this->trickRepository->findAll();
        $users = $this->userRepository->findAll();
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 200; $i++) {
            $chatPost = new ChatPost();
            $chatPost
                ->setMessage($faker->sentences($this->randomNumber(1,8), true))
                ->setUser($faker->randomElement($users))
                ->setTrick($faker->randomElement($tricks))
                ->setDateUpdate($faker->dateTime);

            $manager->persist($chatPost);
        }

        $manager->flush();
    }

    private function randomNumber($minNumber, $maxNumber)
    {
        try {
            return random_int($minNumber, $maxNumber);
        } catch (\Exception $e) {
            return 1;
        }
    }

    public function getDependencies()
    {
        return array(
            TrickFixture::class,
            UserFixture::class
        );
    }
}

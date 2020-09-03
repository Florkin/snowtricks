<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $trick = new Trick();
            $trick
                ->setTitle($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setDifficulty($faker->numberBetween(1, 5))
                ->setVisible(1)
                ->setDateUpdate($faker->dateTime);


            // We have to random how many categories the trick will be associated to
            try {
                $numberOfCategory = random_int(1, 4);
            } catch (\Exception $e) {
                $numberOfCategory = 1;
            }

            for ($j = 0; $j < $numberOfCategory; $j++) {
                try {
                    $trick->addCategory($this->getReference("ref_" . random_int(0, 9)));
                } catch (\Exception $e) {
                    $trick->addCategory($this->getReference("ref_" . 1));
                }
            }


            $manager->persist($trick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CategoryFixture::class
        );
    }
}

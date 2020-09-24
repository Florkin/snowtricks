<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 50; $i++) {
            $trick = new Trick();
            $trick
                ->setTitle($i + 1 . "-" . $faker->words(3, true))
                ->setDescription($faker->sentences(20, true))
                ->setDifficulty($faker->numberBetween(1, 5))
                ->setVisible(1)
                ->setDateUpdate($faker->dateTime);


            // We have to random how many categories the trick will be associated to
            $numberOfCategories = $this->randomNumber(1, 4);

            for ($j = 0; $j < $numberOfCategories; $j++) {
                $trick->addCategory($this->getReference("ref_" . $this->randomNumber(0, 9)));
            }
            $manager->persist($trick);
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
            CategoryFixture::class
        );
    }
}

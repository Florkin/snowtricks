<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 6; $i++) {
            $category = new Category();
            $category
                ->setTitle($faker->words(2, true))
                ->setDescription($faker->sentences(3, true));

            $manager->persist($category);

            $this->addReference("catref_" . $i, $category);
        }

        $manager->flush();
    }
}

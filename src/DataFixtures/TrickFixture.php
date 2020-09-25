<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 50; $i++) {
            $trick = new Trick();

            $filenames = $this->fakeUploadPictures();
            foreach ($filenames as $filename) {
                $newPic = (new Picture())->setFilename($filename);
                $manager->persist($newPic);
                $trick->addPicture($newPic);
            }

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

    private function fakeUploadPictures()
    {
        $fileSystem = new Filesystem();
        $this->fileUploader->setTargetDirectory("images/tricks");

        $numberOfImages = $this->randomNumber(1, 10);
        $filenames = [];

        for ($j = 0; $j < $numberOfImages; $j++) {
            $originalPath = $this->randomPic(__DIR__ . "/imagesFixtures");
            $uniqueName = "img" . $j . ".jpg";
            $targetPath = sys_get_temp_dir() . '/' . $uniqueName;
            $fileSystem->copy($originalPath, $targetPath, false);
            $picture = new UploadedFile($targetPath, $uniqueName, "image/jpeg", null, true);
            $filenames[$j] = $this->fileUploader->upload($picture);
        }

        return $filenames;
    }

    function randomPic($dir)
    {
        $files = glob($dir . '/*.*');
        $file = array_rand($files);
        return $files[$file];
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

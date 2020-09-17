<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\Repository\PictureRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;
    /**
     * @var PictureRepository
     */
    private $pictureRepository;
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * TrickFixture constructor.
     * @param UploaderHelper $uploaderHelper
     * @param PictureRepository $pictureRepository
     * @param CacheManager $cacheManager
     */
    public function __construct(UploaderHelper $uploaderHelper, PictureRepository $pictureRepository, CacheManager $cacheManager)
    {

        $this->uploaderHelper = $uploaderHelper;
        $this->pictureRepository = $pictureRepository;
        $this->cacheManager = $cacheManager;
    }

    public function load(ObjectManager $manager)
    {
        $this->cleanImagesFolders();

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 50; $i++) {
            $trick = new Trick();
            $trick
                ->setTitle($i + 1 . "-" . $faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setDifficulty($faker->numberBetween(1, 5))
                ->setVisible(1)
                ->setDateUpdate($faker->dateTime);

            $pictures = $this->fakeUploadPictures();
            $trick->setPictureFiles($pictures);

            // We have to random how many categories the trick will be associated to
            $numberOfCategories = $this->randomNumber(1, 4);

            for ($j = 0; $j < $numberOfCategories; $j++) {
                $trick->addCategory($this->getReference("ref_" . $this->randomNumber(0, 9)));
            }
            $manager->persist($trick);
        }

        $manager->flush();
    }


    private function cleanImagesFolders()
    {
        $this->cacheManager->remove();
        $path = __DIR__ . "/../../public/images/tricks/*";
        $files = glob($path);
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
    }

    private function fakeUploadPictures()
    {
        $numberOfImages = $this->randomNumber(1, 10);

        $pictures = [];

        for ($j = 0; $j < $numberOfImages; $j++) {
            $originalPath = $this->randomPic(__DIR__ . "/FixturesImages");
            $uniqueName = "img" . $j . ".jpg";
            $fileSystem = new Filesystem();
            $targetPath = sys_get_temp_dir() . '/' . $uniqueName;
            $fileSystem->copy($originalPath, $targetPath, false);
            $pictures[$j] = new UploadedFile($targetPath, $uniqueName, "image/jpeg", null, true);
        }
        return $pictures;
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

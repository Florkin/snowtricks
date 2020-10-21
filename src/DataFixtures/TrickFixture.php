<?php

namespace App\DataFixtures;

use App\Entity\EmbedVideo;
use App\Entity\Picture;
use App\Entity\Trick;
use App\Service\FileUploader;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    const YOUTUBE_VIDEOS = [
        "https://www.youtube.com/watch?v=SQyTWk7OxSI",
        "https://www.youtube.com/watch?v=V9xuy-rVj9w",
        "https://www.youtube.com/watch?v=0uGETVnkujA",
        "https://www.youtube.com/watch?v=1CR0QmCaMTs",
        "https://www.youtube.com/watch?v=1CR0QmCaMTs"
    ];
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * TrickFixture constructor.
     * @param Filesystem $filesystem
     * @param FileUploader $fileUploader
     */
    public function __construct(Filesystem $filesystem, FileUploader $fileUploader)
    {
        $this->fileUploader = $fileUploader;
        $this->filesystem = $filesystem;
    }

    public function load(ObjectManager $manager)
    {
        $this->cleanImagesFolders();
        $this->fileUploader->setTargetDirectory("public/uploads/images/tricks");

        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 30; $i++) {
            $trick = new Trick();

            // Add videos
            $count = count(Self::YOUTUBE_VIDEOS);
            $numberOfVideos = $this->randomNumber(1, $count);
            for ($y = 0; $y < $numberOfVideos; $y++) {
                $newVideo = (new EmbedVideo())->setUrl(Self::YOUTUBE_VIDEOS[$faker->numberBetween(0, $count - 1)]);
                $manager->persist($newVideo);
                $trick->addVideo($newVideo);
            }

            // Add pictures
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
                ->setDateUpdate($faker->dateTime)
                ->setDateAdd($faker->dateTime($trick->getDateUpdate()));


            // We have to random how many categories the trick will be associated to
            $numberOfCategories = $this->randomNumber(1, 4);

            for ($j = 0; $j < $numberOfCategories; $j++) {
                $trick->addCategory($this->getReference("catref_" . $this->randomNumber(0, 5)));
            }

            $trick->setAuthor($this->getReference("userref_" . $this->randomNumber(0, 19)));
            $trick->setUpdatedBy($this->getReference("userref_" . $this->randomNumber(0, 19)));

            $manager->persist($trick);
        }

        $manager->flush();
    }

    private function fakeUploadPictures()
    {
        $numberOfImages = $this->randomNumber(1, 5);
        $filenames = [];

        for ($j = 0; $j < $numberOfImages; $j++) {
            $originalPath = $this->randomPic(__DIR__ . "/imagesFixtures");
            $uniqueName = "img" . $j . ".tmp";
            $targetPath = sys_get_temp_dir() . '/' . $uniqueName;

            $this->filesystem->copy($originalPath, $targetPath, false);
            $picture = new File($targetPath, $uniqueName, "image/jpeg", null, true);
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
            CategoryFixture::class,
            UserFixture::class
        );
    }

    private function cleanImagesFolders()
    {
        $path = "public/uploads/images/tricks/**";
        $files = glob($path);
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
        $path = "public/uploads/images/tricks/thumbs/**";
        $files = glob($path);
        foreach ($files as $file) {
            if (is_file($file))
                unlink($file);
        }
    }
}

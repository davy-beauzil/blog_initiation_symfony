<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Article;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        // Créer 3 catégories
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->word())
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            // Créer entre 4 et 6 articles
            for ($j = 1; $j <= mt_rand(4, 6); $j++) {
                $article = new Article();
                $content = join($faker->paragraphs(5));
                $date = new \DateTimeImmutable();
                $date->setDate($faker->year(2021), $faker->month(12), $faker->dayOfMonth(31));
                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setAuthor($faker->name())
                    ->setCreatedAt($date)
                    ->setUpdatedAt($date)
                    ->setCategory($category);

                $manager->persist($article);

                // Créer entre 4 et 10 commentaires
                for ($k = 1; $k <= mt_rand(4, 10); $k++) {
                    $comment = new Comment();
                    $days = (new DateTimeImmutable())->diff($article->getCreatedAt())->days;
                    $comment->setArticle($article)
                        ->setAuthor($faker->name())
                        ->setContent($faker->paragraph())
                        ->setCreatedAt($faker->dateTimeBetween("-" . $days . "days"));

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}

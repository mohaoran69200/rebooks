<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $booksData = [
            ['title' => 'Asterix aux jeux olympiques', 'category' => 'BD', 'authorName' => 'René Goscinny et Albert Uderzo', 'imageName' => 'asterixjo.webp'],
            ['title' => 'La France d\'après', 'category' => 'Politique', 'authorName' => 'Dominique Reynié', 'imageName' => 'francedapres.webp'],
            ['title' => 'Le Seigneur des anneaux', 'category' => 'Fantastique', 'authorName' => 'J.R.R. Tolkien', 'imageName' => 'lotr.webp'],
            ['title' => 'Le Malade Imaginaire', 'category' => 'Nouvelles', 'authorName' => 'Molière', 'imageName' => 'malade_imaginaire.jpg'],
            ['title' => 'Michael Jordan: The Life', 'category' => 'Biographies', 'authorName' => 'Roland Lazenby', 'imageName' => 'mjthelife.webp'],
            ['title' => 'One Piece', 'category' => 'Mangas', 'authorName' => 'Eiichiro Oda', 'imageName' => 'onepiece.webp'],
            ['title' => 'Paris 2024', 'category' => 'Sports & loisir', 'authorName' => 'Olivier Dacourt', 'imageName' => 'paris2024.webp'],
            ['title' => 'Le Petit Prince', 'category' => 'Romans', 'authorName' => 'Antoine de Saint-Exupéry', 'imageName' => 'petit_prince.webp'],
            ['title' => 'Les opérations de la Seconde Guerre mondiale en 100 cartes', 'category' => 'Histoire', 'authorName' => 'Jean Lopez', 'imageName' => 'ww2cartes.webp'],
            ['title' => 'Un œil dans la nuit', 'category' => 'Thriller', 'authorName' => 'Michael Connelly', 'imageName' => 'oeil_dans_la_nuit.webp'],
        ];

        $users = $manager->getRepository(User::class)->findAll();
        $categoriesRepo = $manager->getRepository(Category::class);

        for ($i = 0; $i < 50; $i++) {
            $user = $users[array_rand($users)];

            $bookData = $booksData[array_rand($booksData)];
            $category = $categoriesRepo->findOneBy(['name' => $bookData['category']]);

            if (!$category) {
                throw new Exception("Catégorie non trouvée : " . $bookData['category']);
            }

            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setAuthorName($bookData['authorName']);
            $book->setDescription($faker->text(500));
            $book->setPrice(rand(10, 50));
            $book->setCreatedAt(new DateTimeImmutable());
            $book->setCategory($category);
            $book->setUser($user);

            // Associe l’image réelle si elle existe, sinon placeholder
            $imageName = $bookData['imageName'] ?? 'placeholder.png';
            $book->setImageName($imageName);

            $manager->persist($book);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoryFixtures::class
        ];
    }
}

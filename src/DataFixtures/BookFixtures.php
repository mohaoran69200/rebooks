<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Liste des titres avec leurs catégories et auteurs
        $booksData = [
            ['title' => 'Asterix aux jeux olympiques', 'category' => 'BD', 'author' => 'René Goscinny et Albert Uderzo'],
            ['title' => 'La France d\'après', 'category' => 'Politique', 'author' => 'Dominique Reynié'],
            ['title' => 'Le Seigneur des anneaux', 'category' => 'Fantastique', 'author' => 'J.R.R. Tolkien'],
            ['title' => 'Le Malade Imaginaire', 'category' => 'Nouvelles', 'author' => 'Molière'],
            ['title' => 'Michael Jordan: The Life', 'category' => 'Biographies', 'author' => 'Roland Lazenby'],
            ['title' => 'Un œil dans la nuit', 'category' => 'Thriller', 'author' => 'Michael Connelly'],
            ['title' => 'One Piece', 'category' => 'Mangas', 'author' => 'Eiichiro Oda'],
            ['title' => 'Le Petit Prince', 'category' => 'Romans', 'author' => 'Antoine de Saint-Exupéry'],
            ['title' => 'Les opérations de la Seconde Guerre mondiale en 100 cartes', 'category' => 'Histoire', 'author' => 'Jean Lopez'],
            ['title' => 'Paris 2024', 'category' => 'Sports & loisir', 'author' => 'Olivier Dacourt']
        ];

        // Récupération des utilisateurs (au moins 50 utilisateurs)
        $users = $manager->getRepository(User::class)->findAll();
        $categoriesRepo = $manager->getRepository(Category::class);

        // Création de 50 livres
        for ($i = 0; $i < 50; $i++) {
            // Sélectionner un utilisateur au hasard
            $user = $users[array_rand($users)];

            // Choisir une catégorie au hasard
            $bookData = $booksData[array_rand($booksData)];
            $category = $categoriesRepo->findOneBy(['name' => $bookData['category']]);

            if (!$category) {
                throw new \Exception("Catégorie non trouvée : " . $bookData['category']);
            }

            // Créer un livre
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setAuthorName($bookData['author']);
            $book->setDescription($faker->text(200)); // Description aléatoire
            $book->setPrice(rand(10, 50)); // Prix entre 10 et 50
            $book->setCreatedAt(new DateTimeImmutable());
            $book->setCategory($category);
            $book->setUser($user);
            $book->setImageName('placeholder.jpg'); // Image par défaut (remplacer plus tard si nécessaire)

            $manager->persist($book);
        }

        $manager->flush();
    }

    // Méthode pour lier les dépendances entre fixtures
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,     // Les utilisateurs doivent être créés avant les livres
            CategoryFixtures::class  // Les catégories doivent être créées avant les livres
        ];
    }
}

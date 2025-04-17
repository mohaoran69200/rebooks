<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 17; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com");
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);

            $username = strtolower($user->getFirstName()) . strtoupper(substr($user->getLastName(), 0, 1));
            $user->setUsername($username);

            $user->setPhoneNumber($faker->phoneNumber);
            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(true);

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
            $user->setPassword($hashedPassword);

            $user->setCreatedAt($faker->dateTimeBetween('-1 years', 'now'));
            $user->setUpdatedAt(new \DateTime());

            $manager->persist($user);
        }

        // Création d’un admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setUsername('adminU');
        $admin->setPhoneNumber('0600000000');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());
        $manager->persist($admin);

        $manager->flush();
    }
}

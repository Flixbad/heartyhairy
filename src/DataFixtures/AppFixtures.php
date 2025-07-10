<?php

namespace App\DataFixtures;

use App\Entity\Dish;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
  {
  }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $regularUser = new User();
  $regularUser
    ->setEmail('hearty&hairy@food.com')
    ->setPassword($this->hasher->hashPassword($regularUser, 'test'));

  $manager->persist($regularUser);

  $adminUser = new User();
  $adminUser
    ->setEmail('hearty&hairy@admin.com')
    ->setRoles(['ROLE_ADMIN'])
    ->setPassword($this->hasher->hashPassword($adminUser, 'test'));

  $manager->persist($adminUser);

        $categories = [];

        // Créons quelques catégories
        foreach (['Entrée', 'Plat', 'Dessert', 'Boisson'] as $catName) {
            $category = new Category();
            $category->setName($catName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Créons des plats
        for ($i = 0; $i < 20; $i++) {
            $dish = new Dish();
            $dish->setName($faker->words(2, true));
            $dish->setPrice($faker->randomFloat(2, 5, 3));
            $dish->setDescription($faker->sentence(8));
            $dish->setCategory($faker->randomElement($categories));
            $manager->persist($dish);
        }

        $manager->flush();
    }
}
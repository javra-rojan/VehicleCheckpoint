<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{    
    /**
     * faker
     *
     * @var Generator
     */
    protected $faker;
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        // $this->faker = Factory::create();
        // $this->faker = Factory::create('FR_Fr');
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}

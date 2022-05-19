<?php

namespace App\DataFixtures;

use App\Entity\Driver;
use App\Entity\ParkingDetail;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DriverFixture extends Fixture
{
     /**
     * faker
     *
     * @var Generator
     */
    protected $faker;
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $driver = new Driver();
            $driver->setName('name' .$i);
            $driver->setPhone(00000+$i);
            $driver->setAddress('address-' .$i);
            $driver->setLicenseNo(880000 + $i);
            // $driver->addParkingDetail($this->getReference(ParkingDetail::DRIVER_PARKING_REFERENCE));
            $manager->persist($driver);
            $manager->flush();
            // $this->addReference('driverobj', $driver);
          }
    }
}

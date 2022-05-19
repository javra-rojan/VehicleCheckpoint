<?php

namespace App\DataFixtures;

use App\Entity\Driver;
use App\Entity\ParkingDetail;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class ParkingDetailFixture extends Fixture implements DependentFixtureInterface
{
    public const DRIVER_PARKING_REFERENCE = 'driver-parking';
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $record = new ParkingDetail();
            $record->setLocation('parking location-' . $i);
            $record->setPlateNo('BAGMATI-' . $i);
            $record->setEntryAt(new \DateTime('@' . strtotime('now')));
            if ($i % 3 == 0) {
                $exit_date = new \DateTime('@' . strtotime('now'));
                $exit_date->add(new \DateInterval('PT2H'));
                $record->setExitAt($exit_date);
            }

            $record->setTicket(mt_rand(0000000000, 9999999999));
            $driver = $this->em->getRepository(Driver::class)->find(1);
            $record->setDriver($driver);
            $manager->persist($record);
            // $this->addReference(self::DRIVER_PARKING_REFERENCE, $record);
            $manager->flush();
        }
    }
    public function getDependencies()
    {
        return [
            DriverFixture::class,
        ];
    }
}

<?php

namespace App\Controller;
use App\Entity\Driver;
use App\Entity\ParkingDetail;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ParkingDetailRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\IsNull;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;

class ParkingDetailController extends AbstractController
{       
    private $driver_repo, $entityManager, $park_repo;
    public function __construct(DriverRepository $repo, ManagerRegistry $doctrine, ParkingDetailRepository $park_repo)
    {   
        $this->entityManager = $doctrine->getManager();
        $this->driver_repo = $repo;
        $this->park_repo = $park_repo;   
    }

    // home page
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        $records = $em->getRepository(ParkingDetail::class)->findBy([],['id' => 'DESC']);
        return $this->render('home/index.html.twig', [
            'records' => $records,
        ]);
    }

    // create new parking entry
    #[Route('/parking/create', name:"parking_entry", methods:['POST'])]
    public function store(Request $request, DriverRepository $repo){
        $driver = $this->getDriverRecord($request->request->get('license_number'), $this->entityManager);
        $record = new ParkingDetail();
        $record->setDriver($driver);
        $record->setLocation("JAVRA PARKING");
        $record->setPlateNo(mt_rand(00000, 11111));
        $record->setEntryAt(new DateTime('@' .strtotime("now")));
        $record->setTicket(mt_rand(0000000000, 9999999999));
        $this->entityManager->persist($record);
        $this->entityManager->flush();
        dd('new vehicle entry recorded');
    }

    // close parking entry
    #[Route('/parking/close', name:"parking_exit", methods:['POST'])]
    public function close(Request $request, ParkingDetailRepository $park_repo){
        $record = $park_repo->findOneBy(
            ['ticket' => $request->request->get('ticket_no'),
             'PlateNo' => $request->request->get('plate_number')   
            ]
        );  
        if( is_null($record->getExitAt()) ){
            $record->setExitAt(new DateTime('@'.strtotime("now")));
            $this->entityManager->persist($record);
            $this->entityManager->flush();
            return new Response('vehicle exit for ticket id ' .$record->getTicket());
        }
        return new Response("ticket already closed");
    }

    /**
     * @Route("/parking/details", name="parking_details", methods="POST")
     */
    public function show(Request $request, ParkingDetailRepository $park_repo): Response
    {   
        $license_no = $request->request->get('license_number');
        $driver = $this->driver_repo->findOneBy([ 'license_no' => $license_no ]);
        // $records = $driver->getParkingDetails();
        $records = $park_repo->findBy(['driver' => $driver]);
        dd($records);
    }
    
    /**
     * @Route("/parking", name="search_query", methods="GET")
     */
    public function searchByQuery(Request $request){
        $records = $this->park_repo->search($request);
        dd($records);
        return new Response('ok');
    }
    public function getDriverRecord($license_number, $manager){
        $driver = $this->driver_repo->findOneBy([ 'license_no' => $license_number]);
        //create new driver with dummy data if driver doesnot exists
        if( !isset($driver) ){
            $driver = new Driver();
            $driver->setName('dummy name');
            $driver->setPhone('dummy number');
            $driver->setAddress('dummy address');
            $driver->setLicenseNo($license_number);
            $manager->persist($driver);
            $manager->flush();
        }
        return $driver;
    }
}

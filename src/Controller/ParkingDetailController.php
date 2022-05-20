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
        $record->setEntryAt(new \DateTime('@' . strtotime('now')));
        $record->setTicket(mt_rand(0000000000, 9999999999));
        $this->entityManager->persist($record);
        $this->entityManager->flush();
        dd('new vehicle entry recorded');
    }

    // close parking entry
    #[Route('/parking/close', name:"parking_exit", methods:['POST'])]
    public function close(Request $request, ParkingDetailRepository $park_repo){
        $record = $park_repo->findOneBy(['ticket' => $request->request->get('ticket_no')]);
        if( is_null($record->getExitAt()) ){
            $record->setExitAt(new DateTime('@' .strtotime("now")));
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
        // $records = $this->park_repo->search($request);
        
        $park_date =  $request->query->get('date');
        // $park_date = new DateTime('@' .$park_date);
        $park_date = date('Y-m-d H:i:s',strtotime($park_date));
        // dd($park_date);

        $records = $this->park_repo->findOneBy(['ticket' => 9375637303])->getEntryAt();
        $records = $records->format('Y-m-d H:i:s');

    // ---find by date 
        // $dt       = new DateTime('@' . $timestamp); // Should take care about timezones if you need
        // $dt_begin = $dt->format('Y-m-d 00:00:00');
        // $dt_end   = $dt->format('Y-m-d 23:59:59');
        // $sql = "SELECT * FROM  table WHERE timestamp BETWEEN UNIX_TIMESTAMP('$dt_begin') AND UNIX_TIMESTAMP('$dt_end')";






        // $license_no = $request->query->get('license_number');
        // $plate_no = $request->query->get('plate_number');   
        // $driver = $this->driver_repo->findBy(['license_no' => $license_no]);
        // if( !empty($plate_no) && empty($license_no) ){
        //     // dd("plate only") ;
        //     $records =  $this->park_repo->findBy(['PlateNo' => $plate_no]);
        // }
        // if ( empty($plate_no) && !empty($license_no)){
        //     // dd('license only');
        //     $records =  $this->park_repo->findBy(['driver' => $driver]);
        // }
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

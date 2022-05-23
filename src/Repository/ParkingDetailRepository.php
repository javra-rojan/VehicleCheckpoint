<?php

namespace App\Repository;

use App\Entity\ParkingDetail;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\ORM\Query\AST\WhereClause;
use Doctrine\ORM\Query\Expr;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ParkingDetail>
 *
 * @method ParkingDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParkingDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParkingDetail[]    findAll()
 * @method ParkingDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParkingDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParkingDetail::class);
    }

    public function add(ParkingDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ParkingDetail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function search($request){
        $qb = $this->createQueryBuilder('p');
        $result = $qb->select('p')
            ->innerJoin('App\Entity\Driver', 'd', Expr\Join::WITH , 'd=p.driver');   
        $parameters = [];
        $license_no = $request->query->get('license_number');
        $plate_no = $request->query->get('plate_number'); 
        $date = $request->query->get('date');
        // dd($date);
        // $fn = 'where';
        if( !empty($license_no) ){
            $qb->andWhere('d.license_no = :license_no');
            $parameters['license_no'] = $license_no;
            // $fn = 'andWhere';
        }
        if( !empty($plate_no) ){
            $qb->andWhere('p.PlateNo like :plate');
            $parameters['plate'] = $plate_no;
            // $fn = 'andWhere';  
        }
        if( !empty($date)){
            $date = new DateTime($date);
            $qb->andWhere('p.EntryAt between :from AND :to');
            $parameters['from'] = $date->format('Y-m-d 00:00:00');
            $parameters['to'] = $date->format('Y-m-d 23:59:59');
        }
        if (count($parameters)) {
            $qb->setParameters($parameters);
        }
        return $qb->getQuery()->getResult();
    }
    public function searchByDate($date){
        $from = $date->format('Y-m-d 00:00:00');
        $to =  $date->format('Y-m-d 23:59:59');
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.EntryAt between :from AND :to')
            ->setParameter('from', $from)->setParameter('to', $to);
        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return ParkingDetail[] Returns an array of ParkingDetail objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ParkingDetail
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

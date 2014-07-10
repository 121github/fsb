<?php

namespace Fsb\AppointmentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * AppointmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AppointmentRepository extends EntityRepository
{
	/**
	 * Get The appointments of a recruiter and in a particular month
	 *
	 * @param int $recruiter_id
	 * @param int $month
	 * @param int $year
	 * 
	 * @return array Appointments
	 */
	public function findNumAppointmentsByRecruiterAndByMonth($recruiter_id,$month,$year, $projects = null, $outcomes = null, $postcode_lat = null, $postcode_lon = null, $distance = null) {
	
		$em = $this->getEntityManager();
		
		$query = $em->createQueryBuilder()
		->select(array('SUBSTRING(a.startDate, 9, 2) AS day', 'COUNT(a.id) AS numapp'))
		->from('AppointmentBundle:Appointment', 'a')
		->innerJoin('a.appointmentDetail', 'ad')
		->innerJoin('ad.address', 'adr')
		->where('a.recruiter = :recruiter_id')
		->andWhere('SUBSTRING(a.startDate, 6, 2) = :month')
		->andWhere('SUBSTRING(a.startDate, 1, 4) = :year')
		->groupBy('day')
		->orderBy('a.startDate', 'ASC')
		
		->setParameter('recruiter_id', $recruiter_id)
		->setParameter('month', (int)$month)
		->setParameter('year', (int)$year)
		;
		
		if (count($projects) > 0) {
			$query
			->andWhere('ad.project IN (:projects)')
			->setParameter('projects', $projects);
		}
		
		if (count($outcomes) > 0) {
			$query
			->andWhere('ad.outcome IN (:outcomes)')
			->setParameter('outcomes', $outcomes);
		}
		
		if ($postcode_lat && $postcode_lon && $distance) {
			
			$query
			->andWhere($query->expr()->between(':lat', 'adr.lat - :distance', 'adr.lat + :distance'))
			->andWhere($query->expr()->between(':lon', 'adr.lon - :distance', 'adr.lon + :distance'))
			->andWhere('
					((((	
						ACOS(
							SIN(:lat*PI()/180) * SIN(adr.lat*PI()/180) +
							COS(:lat*PI()/180) * COS(adr.lat*PI()/180) * COS((:lon - adr.lon)*PI()/180)
						)
					)*180/PI())*160*1.1515)) <= :distance')
			->setParameter('lat', $postcode_lat)
						->setParameter('lon', $postcode_lon)
						->setParameter('lat', $postcode_lat)
						->setParameter('distance', $distance)
			;
		} 
		
		$appointment_ar = $query->getQuery()->getResult();
	
		return $appointment_ar;
	}
	
	/**
	 * Get The appointments of a recruiter and in a particular month
	 *
	 * @param int $recruiter_id
	 * @param int $month
	 * @param int $year
	 *
	 * @return array Appointments
	 */
	public function findAppointmentsByRecruiterAndByMonth($recruiter_id,$month,$year, $projects = null, $outcomes = null, $postcode_lat = null, $postcode_lon = null, $distance = null) {
	
		$em = $this->getEntityManager();
	
		$query = $em->createQueryBuilder()
		->select('a.id, ad.title, adr.lat, adr.lon')
		->from('AppointmentBundle:Appointment', 'a')
		->innerJoin('a.appointmentDetail', 'ad')
		->innerJoin('ad.address', 'adr')
		->where('a.recruiter = :recruiter_id')
		->andWhere('SUBSTRING(a.startDate, 6, 2) = :month')
		->andWhere('SUBSTRING(a.startDate, 1, 4) = :year')
		->orderBy('a.startDate', 'ASC')
	
		->setParameter('recruiter_id', $recruiter_id)
		->setParameter('month', (int)$month)
		->setParameter('year', (int)$year)
		;
	
		if (count($projects) > 0) {
			$query
			->andWhere('ad.project IN (:projects)')
			->setParameter('projects', $projects);
		}
	
		if (count($outcomes) > 0) {
			$query
			->andWhere('ad.outcome IN (:outcomes)')
			->setParameter('outcomes', $outcomes);
		}
	
		if ($postcode_lat && $postcode_lon && $distance) {
			
			$query
			->andWhere($query->expr()->between(':lat', 'adr.lat - :distance', 'adr.lat + :distance'))
			->andWhere($query->expr()->between(':lon', 'adr.lon - :distance', 'adr.lon + :distance'))
			->andWhere('
					((((	
						ACOS(
							SIN(:lat*PI()/180) * SIN(adr.lat*PI()/180) +
							COS(:lat*PI()/180) * COS(adr.lat*PI()/180) * COS((:lon - adr.lon)*PI()/180)
						)
					)*180/PI())*160*1.1515)) <= :distance')
			->setParameter('lat', $postcode_lat)
						->setParameter('lon', $postcode_lon)
						->setParameter('lat', $postcode_lat)
						->setParameter('distance', $distance)
			;
		}
	
		$appointment_ar = $query->getQuery()->getResult();
	
		return $appointment_ar;
	}
	
	/**
	 * Get The appointments of a recruiter and in a particular day
	 *
	 * @param int $recruiter_id
	 * @param $day
	 * @param int $month
	 * @param int $year
	 *
	 * @return array Appointments
	 */
	public function findAppointmentsByRecruiterAndByDay($recruiter_id,$day,$month,$year, $projects = null, $outcomes = null, $postcode_lat = null, $postcode_lon = null, $distance = null) {
	
		$em = $this->getEntityManager();
	
		$query = $em->createQueryBuilder()
		->select(array(
				'SUBSTRING(a.startDate, 11, 3) AS hour', 
				'SUBSTRING(a.startDate, 15, 2) AS minute',
				'a.id, ad.title', 
				'ad.comment',
				'ud.firstname as recruiter',
				'p.name as project',
				'o.name as outcome',
				'ad.outcomeReason',
				'ad.recordRef',
				'adr.postcode',
				'adr.lat',
				'adr.lon'
		))
		->from('AppointmentBundle:Appointment', 'a')
		->innerJoin('a.appointmentDetail', 'ad')
		->innerJoin('ad.project', 'p')
		->innerJoin('ad.outcome', 'o')
		->innerJoin('ad.address', 'adr')
		->innerJoin('a.recruiter', 'u')
		->innerJoin('u.userDetail', 'ud')
		->where('a.recruiter = :recruiter_id')
		->andWhere('SUBSTRING(a.startDate, 9, 2) = :day')
		->andWhere('SUBSTRING(a.startDate, 6, 2) = :month')
		->andWhere('SUBSTRING(a.startDate, 1, 4) = :year')
		->orderBy('a.startDate', 'ASC')
		
		->setParameter('recruiter_id', $recruiter_id)
		->setParameter('day', (int)$day)
		->setParameter('month', (int)$month)
		->setParameter('year', (int)$year)
		;
		
		if (count($projects) > 0) {
			$query
			->andWhere('ad.project IN (:projects)')
			->setParameter('projects', $projects);
		}
		
		if (count($outcomes) > 0) {
			$query
			->andWhere('ad.outcome IN (:outcomes)')
			->setParameter('outcomes', $outcomes);
		}
		
		if ($postcode_lat && $postcode_lon && $distance) {
			
			$query
			->andWhere($query->expr()->between(':lat', 'adr.lat - :distance', 'adr.lat + :distance'))
			->andWhere($query->expr()->between(':lon', 'adr.lon - :distance', 'adr.lon + :distance'))
			->andWhere('
					((((	
						ACOS(
							SIN(:lat*PI()/180) * SIN(adr.lat*PI()/180) +
							COS(:lat*PI()/180) * COS(adr.lat*PI()/180) * COS((:lon - adr.lon)*PI()/180)
						)
					)*180/PI())*160*1.1515)) <= :distance')
			->setParameter('lat', $postcode_lat)
						->setParameter('lon', $postcode_lon)
						->setParameter('lat', $postcode_lat)
						->setParameter('distance', $distance)
			;
		}
		
		$appointment_ar = $query->getQuery()->getResult();
	
		return $appointment_ar;
	}
	
	/**
	 * Get The appointments of a recruiter and since a particular day
	 *
	 * @param int $recruiter_id
	 * @param $day
	 * @param int $month
	 * @param int $year
	 *
	 * @return array Appointments
	 */
	public function findAppointmentsByRecruiterFromDay($recruiter_id,$day,$month,$year, $projects = null, $outcomes = null, $postcode_lat = null, $postcode_lon = null, $distance = null) {
	
		$em = $this->getEntityManager();
	
		
		$query = $em->createQueryBuilder()
		->select(array(
				'a.startDate as date, a.id, ad.title, ad.comment',
				'ud.firstname as recruiter',
				'p.name as project',
				'o.name as outcome',
				'ad.outcomeReason',
				'ad.recordRef',
				'adr.postcode',
				'adr.lat',
				'adr.lon'
		))
		->from('AppointmentBundle:Appointment', 'a')
		->innerJoin('a.appointmentDetail', 'ad')
		->innerJoin('ad.project', 'p')
		->innerJoin('ad.outcome', 'o')
		->innerJoin('ad.address', 'adr')
		->innerJoin('a.recruiter', 'u')
		->innerJoin('u.userDetail', 'ud')
		->where('a.recruiter = :recruiter_id')
		->andWhere('a.recruiter = :recruiter_id')
		->andWhere('a.startDate > :date')
		->orderBy('a.startDate', 'ASC')
		
		->setParameter('recruiter_id', $recruiter_id)
		->setParameter('date', new \DateTime($year.'-'.$month.'-'.$day.' 00:00:00'))
		;
		
		if (count($projects) > 0) {
			$query
			->andWhere('ad.project IN (:projects)')
			->setParameter('projects', $projects);
		}
		
		if (count($outcomes) > 0) {
			$query
			->andWhere('ad.outcome IN (:outcomes)')
			->setParameter('outcomes', $outcomes);
		}
		
		if ($postcode_lat && $postcode_lon && $distance) {
			
			$query
			->andWhere($query->expr()->between(':lat', 'adr.lat - :distance', 'adr.lat + :distance'))
			->andWhere($query->expr()->between(':lon', 'adr.lon - :distance', 'adr.lon + :distance'))
			->andWhere('
					((((	
						ACOS(
							SIN(:lat*PI()/180) * SIN(adr.lat*PI()/180) +
							COS(:lat*PI()/180) * COS(adr.lat*PI()/180) * COS((:lon - adr.lon)*PI()/180)
						)
					)*180/PI())*160*1.1515)) <= :distance')
			->setParameter('lat', $postcode_lat)
						->setParameter('lon', $postcode_lon)
						->setParameter('lat', $postcode_lat)
						->setParameter('distance', $distance)
			;
		}
		
		$appointment_ar = $query->getQuery()->getResult();
	
		return $appointment_ar;
	}
	
	/**
	 * 
	 * Find the appointments that are between two dates for a particular recruiter
	 * 
	 * @param \DateTime $startDate
	 * @param \DateTime $endDate
	 */
	public function findAppointmentsWithCollision(\DateTime $startDate, \DateTime $endDate, $recruiter_id) {
		$em = $this->getEntityManager();
		
		$query = $em->createQueryBuilder()
		->select(array('a.id'))
		->from('AppointmentBundle:Appointment', 'a')
		->innerJoin('a.appointmentDetail', 'ad')
		->innerJoin('ad.address', 'adr')
		->where('a.recruiter = :recruiter_id')
		->andWhere('
				(((SUBSTRING(a.startDate, 1, 10)) <= SUBSTRING(:startDate, 1, 10) AND (SUBSTRING(a.endDate, 1, 10)) >= SUBSTRING(:endDate, 1, 10)) OR
				((SUBSTRING(a.startDate, 1, 10)) <= SUBSTRING(:startDate, 1, 10) AND (SUBSTRING(a.endDate, 1, 10)) <= SUBSTRING(:endDate, 1, 10) AND (SUBSTRING(a.endDate, 1, 10)) > SUBSTRING(:startDate, 1, 10)) OR
				((SUBSTRING(a.startDate, 1, 10)) >= SUBSTRING(:startDate, 1, 10) AND (SUBSTRING(a.endDate, 1, 10)) <= SUBSTRING(:endDate, 1, 10)) OR
				((SUBSTRING(a.startDate, 1, 10)) >= SUBSTRING(:startDate, 1, 10) AND (SUBSTRING(a.endDate, 1, 10)) >= SUBSTRING(:endDate, 1, 10) AND (SUBSTRING(a.startDate, 1, 10)) <= SUBSTRING(:endDate, 1, 10)))
				AND
				(((SUBSTRING(a.startDate, 12, 8)) <= SUBSTRING(:startDate, 12, 8) AND (SUBSTRING(a.endDate, 12, 8)) >= SUBSTRING(:endDate, 12, 8)) OR
				((SUBSTRING(a.startDate, 12, 8)) <= SUBSTRING(:startDate, 12, 8) AND (SUBSTRING(a.endDate, 12, 8)) <= SUBSTRING(:endDate, 12, 8) AND (SUBSTRING(a.endDate, 12, 8)) > SUBSTRING(:startDate, 12, 8)) OR
				((SUBSTRING(a.startDate, 12, 8)) >= SUBSTRING(:startDate, 12, 8) AND (SUBSTRING(a.endDate, 12, 8)) <= SUBSTRING(:endDate, 12, 8)) OR
				((SUBSTRING(a.startDate, 12, 8)) >= SUBSTRING(:startDate, 12, 8) AND (SUBSTRING(a.endDate, 12, 8)) >= SUBSTRING(:endDate, 12, 8) AND (SUBSTRING(a.startDate, 12, 8)) <= SUBSTRING(:endDate, 12, 8)))
		')
		->orderBy('a.startDate', 'ASC')
		
		->setParameter('recruiter_id', $recruiter_id)
		->setParameter('startDate', $startDate)
		->setParameter('endDate', $endDate)
		;
		
		$appointment_ar = $query->getQuery()->getResult();
		
		return $appointment_ar;
	}
}

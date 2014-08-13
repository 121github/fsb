<?php

namespace Fsb\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
	/**
	 * Get All the users by a role
	 *
	 * @param Role $role Get the Users  by a role
	 *
	 * @return array Recruiters
	 */
	public function findUsersByRole($role){
	
		$query = $this->findUsersByRoleQuery($role);
		
		$user_ar = $query->getQuery()->getResult();
	
		return $user_ar;
	}
	
	/**
	 * Get the Query to find all the users by a role
	 *
	 * @param Role $role Get the Users  by a role
	 *
	 * @return array Recruiters
	 */
	public function findUsersByRoleQuery($role)
	{
		$eManager = $this->getEntityManager();
	
		$query = $eManager->createQueryBuilder()
			->select('u')
			->from('UserBundle:User', 'u')
			->innerJoin('u.role', 'ur')
			->where('ur.name = :role')
			->orderBy('ur.name', 'ASC')
			->setParameter('role', $role)	
		;
		
		return $query;
	}
	
	
	/**
	 * Get All Users ordered by firstname
	 *
	 *
	 * @return array Users
	 */
	public function findAllOrderByName($roles = null){
	
		$eManager = $this->getEntityManager();
		
		$query = $eManager->createQueryBuilder()
			->select('u')
			->from('UserBundle:User', 'u')
			->innerJoin('u.userDetail', 'ud')
			->innerJoin('u.role', 'r')
			->orderBy('ud.firstname', 'ASC')	
		;
		
		if ($roles) {
			$query
			->andWhere('r.id IN (:roles)')
			->setParameter('roles', $roles);
		}
	
		$user_ar = $query->getQuery()->getResult();
	
		return $user_ar;
	}
	
	
	/**
	 * Get an user by name and role
	 * 
	 * @param $name
	 * @param $role
	 *
	 * @return User
	 */
	public function findUserByNameAndRole($name, $role){
	
		$eManager = $this->getEntityManager();
	
		
		$query = $eManager->createQueryBuilder()
		->select('u')
		->from('UserBundle:User', 'u')
		->innerJoin('u.userDetail', 'ud')
		->innerJoin('u.role', 'r')
		->where('CONCAT(ud.firstname,\' \',ud.lastname) = :name')
		->andWhere('r.name = :role')
		
		->setParameter('name', $name)
		->setParameter('role', $role)
		;
	
		$user = $query->getQuery()->getResult();
	
		return $user;
	}
}

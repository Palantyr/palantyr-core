<?php
namespace JJSR\Bundle\GameSessionBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RolGameRepository extends EntityRepository
{
	public function findAll()
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT rolGame FROM GameSessionBundle:RolGame rolGame'
		);
		
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
	
	public function findAllActives()
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT rolGame FROM GameSessionBundle:RolGame rolGame
				 WHERE rolGame.active = 1'
		);
	
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}

}
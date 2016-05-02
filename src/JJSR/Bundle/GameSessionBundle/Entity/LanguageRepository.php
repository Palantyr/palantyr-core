<?php
namespace JJSR\Bundle\GameSessionBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LanguageRepository extends EntityRepository
{
	public function findAll()
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT language FROM GameSessionBundle:Language language'
		);
		
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}

}
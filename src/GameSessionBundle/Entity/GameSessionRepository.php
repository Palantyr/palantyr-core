<?php
// src/GameSessionBundle/Entity/GameSessionRepository.php
namespace GameSessionBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GameSessionRepository extends EntityRepository
{
	public function findAll()
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT gameSession FROM GameSessionBundle:GameSession gameSession'
		);
		
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
	
	public function findActiveGameSessionsWithOwner()
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT gameSession AS array_gameSession, user.username 
				FROM GameSessionBundle:GameSession gameSession, UserBundle:User user
				WHERE gameSession.owner_user = user.id'
		);
		
		/*$query = $this->getEntityManager()
		 ->createQuery(
		 'SELECT gameSession.id, gameSession.name, gameSession.owner_user, gameSession.start_date,
		 gameSession.rol_game, gameSession.language, gameSession.standard_view, gameSession.comments,
		 user.username 
		 FROM GameSessionBundle:GameSession gameSession, UserBundle:User user
		 WHERE gameSession.owner_user = user.id'
		 );*/
			
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}		
	}

}
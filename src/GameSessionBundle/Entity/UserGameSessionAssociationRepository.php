<?php
// src/GameSessionBundle/Entity/UserGameSessionAssociationRepository.php
namespace GameSessionBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserGameSessionAssociationRepository extends EntityRepository
{
	public function findByUserAndGameSession($user_id, $game_session_id) 
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT userGameSessionAssociation 
				FROM GameSessionBundle:UserGameSessionAssociation userGameSessionAssociation
				WHERE userGameSessionAssociation.user_id = '.$user_id.'
				AND userGameSessionAssociation.game_session_id = '.$game_session_id.''
		);

		try {
			return $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
	
	public function findByGameSession($game_session_id)
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT userGameSessionAssociation
				FROM GameSessionBundle:UserGameSessionAssociation userGameSessionAssociation
				WHERE userGameSessionAssociation.game_session_id = '.$game_session_id.''
				);
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
}
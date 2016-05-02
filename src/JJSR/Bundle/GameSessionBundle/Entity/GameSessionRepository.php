<?php
namespace JJSR\Bundle\GameSessionBundle\Entity;

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
	
	public function findActiveGameSessions()
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT gameSession AS array_gameSession, user.username AS user_username, 
					rolGame.name AS rolGame_name, language.name AS language_name
				FROM GameSessionBundle:GameSession gameSession, UserBundle:User user, 
					GameSessionBundle:RolGame rolGame, GameSessionBundle:Language language
				WHERE gameSession.owner = user.id 
					AND gameSession.rol_game = rolGame.id 
					AND gameSession.language = language.id'
		);
			
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
	
	public function findCompleteGameSessionById($id)
	{
		$query = $this->getEntityManager()
		->createQuery(
				'SELECT gameSession AS array_gameSession, user.username AS user_username,
					rolGame.name AS rolGame_name, language.name AS language_name
				FROM GameSessionBundle:GameSession gameSession, UserBundle:User user,
					GameSessionBundle:RolGame rolGame, GameSessionBundle:Language language
				WHERE gameSession.owner = user.id
					AND gameSession.rol_game = rolGame.id
					AND gameSession.language = language.id
					AND gameSession.id = '.$id.''
				);
			
		try {
			return $query->getSingleResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}
	}
	/*
	public function addElementsTest()
	{
		$query = $this->getEntityManager()
		->createQuery(
				'INSERT INTO GameSessionBundle:GameSession gameSession'
		);
		INSERT INTO table_name (column1,column2,column3,...)
		VALUES (value1,value2,value3,...);
		
		INSERT INTO `palantir_db`.`language` (`id`, `name`) VALUES ('3', 'France');
		INSERT INTO `palantir_db`.`language` (`id`, `name`) VALUES ('4', 'Germany');
		try {
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e) {
			return null;
		}		
	}
	*/
}
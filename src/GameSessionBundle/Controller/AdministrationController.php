<?php

namespace GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GameSessionBundle\Entity\RolGame;
use GameSessionBundle\Entity\Language;
use GameSessionBundle\Entity\GameSession;
use FOS\UserBundle\Util\UserManipulator as UserManipulator;


class AdministrationController extends Controller
{
    public function indexAction(Request $request)
    {
    	/* 
    	/ DB configuration:
    	// a:0:{}
    	// a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}
    	$userManager = $this->get('fos_user.user_manager');
    	$userManipulator = new UserManipulator($userManager);
    	$userManipulator->promote($this->getUser()->getUsername());
    	$user = $this->getUser()->getRoles();
    	var_dump($user);die();
		*/
    	
    	$data = array();
    	$form = $this->createFormBuilder($data)
    		->add('add_all_to_test', 'submit', array('label' => 'Add all to test'))
    		->add('add_languages', 'submit', array('label' => 'Add Languages'))
    		->add('add_rol_games', 'submit', array('label' => 'Add Rol Games'))
    		->add('delete_all_to_test', 'submit', array('label' => 'Delete all to test'))
    		->add('delete_languages', 'submit', array('label' => 'Delete Languages'))
    		->add('delete_rol_games', 'submit', array('label' => 'Delete Rol Games'))
    		->add('delete_game_sessions', 'submit', array('label' => 'Delete Game Sessions'))
    		->add('delete_user_game_sessions_association', 'submit', array('label' => 'Delete User Game Sessions Association'))
    		->getForm();
    	
    	$form->handleRequest($request);
    	
    	$petition_text = "";
    	
    	if ($form->isValid()) {
    	   	if ($form->get('add_all_to_test')->isClicked()) {
    	   		self::addLanguages();
    	   		self::addRolGames();
    		}
    		elseif ($form->get('add_languages')->isClicked()) {
				self::addLanguages();
    		}
    		elseif ($form->get('add_rol_games')->isClicked()) {
				self::addRolGames();
    		}
    		elseif ($form->get('delete_all_to_test')->isClicked()) {
    			self::deleteLanguages();
    			self::deleteRolGames();
    		}
    		elseif ($form->get('delete_languages')->isClicked()) {
    			 self::deleteLanguages();
    		}
    		elseif ($form->get('delete_rol_games')->isClicked()) {
    			 self::deleteRolGames();
    		}
    		elseif ($form->get('delete_game_sessions')->isClicked()) {
    			self::deleteGameSessions();
    		}
    		elseif ($form->get('delete_user_game_sessions_association')->isClicked()) {
    			self::deleteUserGameSessionAssociation();
    		}
    		$petition_text = "Operation completed successfully";
    	}

        return $this->render('GameSessionBundle:Administration:main_menu.html.twig', 
        		array('form' => $form->createView(), 
        			'petition_text' => $petition_text));
    }
    
    public function addLanguages() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$languages_names = array("spanish", "english");
    	$languages_names_count = count($languages_names);
    	for ($i = 0 ; $i < $languages_names_count ; $i++) {
    		$language = new Language();
    		$language->setName($languages_names[$i]);
    		$em->persist($language);
    	}
    	$em->flush();
    }
    
    public function addRolGames() {
    	$em = $this->getDoctrine()->getEntityManager();
    	$rol_games_names = array("Pathfinder", "D&D 3.5", "Vampire The Masquerade", "Werewolf", "Anima", "Legend of Five Rings", "Star Wars");
    	$rol_game_actives = array(1, 1, 1, 0, 0, 1, 1);
    	$rol_games_names_count = count($rol_games_names);
    	for ($i = 0 ; $i < $rol_games_names_count ; $i++) {
    		$rol_game = new RolGame();
    		$rol_game->setName($rol_games_names[$i])->setActive($rol_game_actives[$i]);
    		$em->persist($rol_game);
    	}
    	$em->flush();
    }
    
    public function deleteLanguages() {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$languages = $this->getDoctrine()
    		->getRepository('GameSessionBundle:Language')
    		->findAll();
    	
    	$languages_count = count($languages); 
    	for ($i = 0 ; $i < $languages_count ; $i++) {
    		$em->remove($languages[$i]);
    	}
    	$em->flush();
    }
    
    public function deleteRolGames() {
    	$em = $this->getDoctrine()->getEntityManager();
    	 
    	$rol_games = $this->getDoctrine()
    	->getRepository('GameSessionBundle:RolGame')
    	->findAll();
    	 
    	$rol_games_count = count($rol_games);
    	for ($i = 0 ; $i < $rol_games_count ; $i++) {
    		$em->remove($rol_games[$i]);
    	}
    	$em->flush();
    }
    
    public function deleteGameSessions() {
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$game_sessions = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->findAll();
    
    	$game_sessions_count = count($game_sessions);
    	for ($i = 0 ; $i < $game_sessions_count ; $i++) {
    		$em->remove($game_sessions[$i]);
    	}
    	$em->flush();
    }
    
    public function deleteUserGameSessionAssociation () {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$user_game_session_association = $this->getDoctrine()
    	->getRepository('GameSessionBundle:UserGameSessionAssociation')
    	->findAll();
    	
    	$user_game_session_association_count = count($user_game_session_association);
    	for ($i = 0 ; $i < $user_game_session_association_count ; $i++) {
    		$em->remove($user_game_session_association[$i]);
    	}
    	$em->flush();
    }
}

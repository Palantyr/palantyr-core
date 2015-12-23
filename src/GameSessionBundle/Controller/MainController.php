<?php

namespace GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GameSessionBundle\Entity\GameSession;
use GameSessionBundle\Entity\RolGame;
use GameSessionBundle\Entity\PasswordGameSession;
use Symfony\Component\HttpFoundation\Response;
use GameSessionBundle\Entity\UserGameSessionAssociation;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('GameSessionBundle:GameSession:main_menu.html.twig');
    }
    
    public function addGameSession($game_session) 
    {
    	$game_session->setOwnerUser($this->getUser()->getId());
    	$game_session->setStartDate(new \DateTime());
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($game_session);
    	$em->flush();
    }
    
    public function createGameSessionAction(Request $request)
    {
    	$game_session = new GameSession();
    	$game_session->setName("Epic Game");
    
    	$form = $this->createFormBuilder($game_session)
    	->add('name', 'text')
    	->add('password', 'password')
    	->add('rol_game', 'entity', array(
    		'required'    => true,
    		'placeholder' => 'Choose the rol game',
		    'class'    => 'GameSessionBundle:RolGame',
		    'property' => 'name',
    		'choices' => $this->getDoctrine()
    					->getRepository('GameSessionBundle:RolGame')
    					->findAllActives()
			))
    	->add('language', 'entity', array(
    		'required'    => true,
    		'placeholder' => 'Choose the session language',
		    'class'    => 'GameSessionBundle:Language',
		    'property' => 'name',
    		'choices' => $this->getDoctrine()
    					->getRepository('GameSessionBundle:Language')
    					->findAll()
			))
    	->add('comments', 'textarea', array(
        	'required' => false))
    	->add('Start', 'submit')
    	->getForm();
    	
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		$game_session->setRolGame($game_session->getRolGame()->getId());
    		$game_session->setLanguage($game_session->getLanguage()->getId());
    		$this->addGameSession($game_session);
    		return $this->redirect($this->generateUrl('join_session', array('session_id' => $game_session->getId())));
    	}

    	return $this->render('GameSessionBundle:GameSession:create.html.twig', array(
    			'form' => $form->createView(),
    	));
    }
    
    public function renderGameSessionAction(Request $request, $game_session_id) 
    {
    	$game_session = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->find($game_session_id);
    	
    	$rol_game = $this->getDoctrine()
    	->getRepository('GameSessionBundle:RolGame')
    	->find($game_session->getRolGame());
    	
    	return $this->render('GameSessionBundle:GameSession:game.html.twig', array(
    			'game_session' => $game_session, 'rol_game' => $rol_game
    	));
    }
    
    public function joinGameSessionAction(Request $request, $game_session_id) 
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$user_game_session_assotiation = $em->getRepository('GameSessionBundle:UserGameSessionAssociation')
    		->findByUserAndGameSession($this->getUser()->getId(), $game_session_id);
    	
    	$allow_access = false;
    	$conected = false;
    	if ($user_game_session_assotiation) {
    		$allow_access = $user_game_session_assotiation->getAllowAccess();
    		$conected = $user_game_session_assotiation->getConected();
    	}
    	
    	if ($conected === true) {
    		return $this->redirectToRoute('login_game_session_rejected', array('game_session_id' => $game_session_id));
    	}
    	
    	if ($allow_access === false) {
    		return $this->redirectToRoute('login_game_session', array('game_session_id' => $game_session_id));
    	}
		else if ($allow_access === true) {
			return self::renderGameSessionAction($request, $game_session_id);
		}
    }

    public function loginGameSessionAction(Request $request, $game_session_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$game_session = $em
    		->getRepository('GameSessionBundle:GameSession')
    		->findCompleteGameSessionById($game_session_id);

    	$password_game_session = new PasswordGameSession();
    	$form = $this->createFormBuilder($password_game_session)
    		->add('password', 'password')
    		->add('Start', 'submit')
    		->getForm();
    			 
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		if ($form->getData()->getPassword() != $game_session[0]['array_gameSession']->getPassword()) {
    			/*
    			$this->get('session')->getFlashBag()->add(
    				'bad_login',
    				'Password incorrect'
    			);
    			*/
    			return $this->redirectToRoute('game_sessions');
    		}
    		
    		else {
    			$user_game_session_assotiation = $em->getRepository('GameSessionBundle:UserGameSessionAssociation')
    			->findByUserAndGameSession($this->getUser()->getId(), $game_session_id);
    			
    			if ($user_game_session_assotiation) {
    				$user_game_session_assotiation->setAllowAccess(true);
    				$em->persist($user_game_session_assotiation);
    				$em->flush();
    			}
    			else {
    				$user_game_session_assotiation = new UserGameSessionAssociation();
    				$user_game_session_assotiation->setUserId($this->getUser()->getId());
    				$user_game_session_assotiation->setGameSessionId($game_session_id);
    				$user_game_session_assotiation->setAllowAccess(true);
    				$user_game_session_assotiation->setConected(false);
    				$em->persist($user_game_session_assotiation);
    				$em->flush();
    			}
    			return $this->redirectToRoute('join_game_session', array('game_session_id' => $game_session_id));
    		}
    	}
    	
    	return $this->render('GameSessionBundle:Security:login_game.html.twig', array(
    		'form' => $form->createView(), 'game_session_array' => $game_session
    	));
    }
    
    public function loginGameSessionRejectedAction($game_session_id) {
    	return self::alreadyConectedRender($game_session_id);
    }
    
    public function alreadyConectedRender($game_session_id) {
    	return $this->render('GameSessionBundle:Security:already_conected_game_session.html.twig');
    }
    
    public function gameSessionsAction(\Symfony\Component\HttpFoundation\Request $request) 
    {
    	$game_sessions_actives = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->findActiveGameSessions();
    	
    	return $this->render('GameSessionBundle:GameSession:game_list.html.twig', array(
    			'game_sessions_actives' => $game_sessions_actives
    	));
    }
}

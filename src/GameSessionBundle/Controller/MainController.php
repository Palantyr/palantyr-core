<?php

namespace GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GameSessionBundle\Entity\GameSession;
use GameSessionBundle\Entity\RolGame;
use GameSessionBundle\Entity\PasswordGameSession;

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
    
    public function renderSessionAction(Request $request, $session_id) 
    {
    	$game_session = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->find($session_id);
    	
    	$rol_game = $this->getDoctrine()
    	->getRepository('GameSessionBundle:RolGame')
    	->find($game_session->getRolGame());

    	$session = $request->getSession();
    	$session_game_sessions = $session->get('game_sessions');
    	$session_game_sessions[$session_id] = false;
    	$session->set('game_sessions', $session_game_sessions);
    	
    	return $this->render('GameSessionBundle:GameSession:game.html.twig', array(
    			'game_session' => $game_session, 'rol_game' => $rol_game
    	));
    }
    
    public function joinSessionAction(Request $request, $session_id) 
    {
    	$session = $request->getSession();
    	//var_dump($session_id);var_dump($session->get('game_sessions'));die();
    	if (!$session->get('game_sessions') || array_key_exists($session_id, $session->get('game_sessions')) != true) {
    		$be_authorized = false;
    	}
    	else {
    		//var_dump($session->get('game_sessions')[$session_id]);die();
    		$be_authorized = $session->get('game_sessions')[$session_id];
    		//var_dump($be_authorized);die();
    	}
    	
    	if ($be_authorized != true) {
    		return self::loginAction($request, $session_id);
    	}
    	else {
			return self::renderSessionAction($request, $session_id);
    	}
    }

    public function loginAction(Request $request, $session_id)
    {
    	$game_session = $this->getDoctrine()
    		->getRepository('GameSessionBundle:GameSession')
    		->findCompleteGameSessionById($session_id);

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
    			return $this->redirect($this->generateUrl('game_sessions'));
    		}
    		
    		else {
    			$session = $request->getSession();
    			$session_game_sessions = $session->get('game_sessions');
    			$session_game_sessions[$session_id] = true;
    			$session->set('game_sessions', $session_game_sessions);
				return self::renderSessionAction($request, $session_id);
    		}
    	}
    	
    	return $this->render('GameSessionBundle:Security:login_game.html.twig', array(
    		'form' => $form->createView(), 'game_session_array' => $game_session
    	));
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

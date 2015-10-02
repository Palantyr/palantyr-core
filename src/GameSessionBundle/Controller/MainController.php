<?php

namespace GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GameSessionBundle\Entity\GameSession;
use GameSessionBundle\Entity\RolGame;
use Symfony\Component\BrowserKit\Response;

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
    	->add('pass', 'password')
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
    
    public function joinSessionAction($session_id) {
    	$game_session = $this->getDoctrine()
        ->getRepository('GameSessionBundle:GameSession')
        ->find($session_id);
    	
    	return $this->render('GameSessionBundle:GameSession:game.html.twig', array(
    			'game_session' => $game_session
    	));
    }

    public function gameSessionsAction(\Symfony\Component\HttpFoundation\Request $request) {
    	
    	$game_sessions_with_owner = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->findActiveGameSessionsWithOwner();
    	
    	$game_sessions_actives = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->findActiveGameSessions();
    	
    	
    	//var_dump($game_sessions_actives); die();
    	return $this->render('GameSessionBundle:GameSession:game_list.html.twig', array(
    			'game_sessions_actives' => $game_sessions_actives
    	));
    }
}

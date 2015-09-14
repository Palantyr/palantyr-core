<?php

namespace GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GameSessionBundle\Entity\GameSession;
use Symfony\Component\BrowserKit\Response;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('GameSessionBundle:GameSession:gameSessions.html.twig');
    }
    
    public function addGameSession($game_session) 
    {
    	//$game_session->setOwnerUser($this->getUser()->getId());
    	$game_session->setOwnerUser("1");
    	$game_session->setStartDate(new \DateTime());
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($game_session);
    	$em->flush();
    }
    
    public function createGameSessionAction(Request $request)
    {
    	$game_session = new GameSession();
    	
    	$game_session->setName("Epic Game");
    	$game_session->setRolGame("1");
    	$game_session->setLanguage("1");
    	$game_session->setStandardView("1");
    
    	$form = $this->createFormBuilder($game_session)
    	->add('name', 'text')
    	->add('pass', 'password')
    	->add('rol_game', 'text')
    	->add('language', 'text')
    	->add('standard_view', 'text')
    	->add('comments', 'textarea', array(
        	'required' => false))
    	->add('Start', 'submit')
    	->getForm();
    	
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		$this->addGameSession($game_session);
    		return $this->redirect($this->generateUrl('join_session', array('session_id' => $game_session->getId())));
    	}

    	return $this->render('GameSessionBundle:GameSession:createGameSesion.html.twig', array(
    			'form' => $form->createView(),
    	));
    }
    
    public function joinSessionAction($session_id) {
    	$game_session = $this->getDoctrine()
        ->getRepository('GameSessionBundle:GameSession')
        ->find($session_id);
    	
    	return $this->render('GameSessionBundle:GameSession:gameSession.html.twig', array(
    			'game_session' => $game_session
    	));
    }

    public function gameSessionsAction(\Symfony\Component\HttpFoundation\Request $request) {
    	
    	$game_sessions_with_owner = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->findActiveGameSessionsWithOwner();
    	//var_dump($game_sessions_with_owner); die();
    	return $this->render('GameSessionBundle:GameSession:gameSessionsList.html.twig', array(
    			'game_sessions_with_owner' => $game_sessions_with_owner
    	));
    }
}

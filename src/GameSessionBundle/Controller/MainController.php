<?php

namespace GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GameSessionBundle\Entity\GameSession;
use GameSessionBundle\Entity\RolGame;
use GameSessionBundle\Entity\UserGameSessionAssociation;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MainController extends Controller
{ 
    public function addGameSession($game_session) 
    {
    	$game_session->setOwner($this->getUser()->getId());
    	$game_session->setStartDate(new \DateTime());
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($game_session);
    	$em->flush();
    }
    
    public function createGameSessionAction(Request $request)
    {
    	$translator = $this->get('translator');
    	
    	$game_session = new GameSession();
    	$game_session->setName("Epic Game");
    	
    	$form = $this->createFormBuilder($game_session, array(
    		'validation_groups' => array('Create')))
    	->add('name', 'text')
    	->add('password', 'password')
    	->add('rol_game', 'entity', array(
    		'required'    => true,
    		'placeholder' => $translator->trans('game_session.create.choose_rol_game'),
		    'class'    => 'GameSessionBundle:RolGame',
		    'property' => 'name',
    		'choices' => $this->getDoctrine()
    					->getRepository('GameSessionBundle:RolGame')
    					->findAllActives()
			))
    	->add('language', 'entity', array(
    		'required'    => true,
    		'placeholder' => $translator->trans('game_session.create.choose_language'),
		    'class'    => 'GameSessionBundle:Language',
		    'property' => 'name',
    		'choices' => $this->getDoctrine()
    					->getRepository('GameSessionBundle:Language')
    					->findAll()
			))
    	->add('comments', 'textarea', array(
        	'required' => false))
        ->add('submit_button', 'submit')
    	->getForm();
    	
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		$game_session->setRolGame($game_session->getRolGame()->getId());
    		$game_session->setLanguage($game_session->getLanguage()->getId());
    		$this->addGameSession($game_session);
    		return $this->redirect($this->generateUrl('join_game_session', array('game_session_id' => $game_session->getId())));
    	}

    	return $this->render('GameSessionBundle:Web:create.html.twig', array(
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
    	
    	return $this->render('GameSessionBundle:GameSession:game_session.html.twig', array(
    			'game_session' => $game_session, 'rol_game' => $rol_game
    	));
    }
    
    public function joinGameSessionAction(Request $request, $game_session_id) 
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$user_game_session_assotiation = $em->getRepository('GameSessionBundle:UserGameSessionAssociation')
    		->findByUserAndGameSession($this->getUser()->getId(), $game_session_id);
    	
    	$allow_access = false;
    	$connected = false;
    	if ($user_game_session_assotiation) {
    		$allow_access = $user_game_session_assotiation->getAllowAccess();
    		$connected = $user_game_session_assotiation->getConnected();
    	}
    	
    	if ($connected === true) {
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

    	if ($game_session['array_gameSession']->getOwner() == $this->getUser()->getId()) {
			return self::loginGameAllowAccess($request, $game_session_id);
    	}
    	
    	else {
	    	$password_game_session = new GameSession();
	    	$form = $this->createFormBuilder($password_game_session, array(
	    			'validation_groups' => array('Login')))
	    		->add('password', 'password')
	    		->add('submit_button', 'submit')
	    		->getForm();
	    			 
	    	$form->handleRequest($request);
	    	
	    	if ($form->isValid()) {
	    		if ($form->getData()->getPassword() != $game_session['array_gameSession']->getPassword()) {
	
	    			$this->get('session')->getFlashBag()->add(
	    				'bad_login',
	    				'game_session.login.incorrect_password'
	    			);
	    		}
	    		
	    		else {
	    			return self::loginGameAllowAccess($request, $game_session_id);
	    		}
	    	}
	    	
	    	return $this->render('GameSessionBundle:Security:login_game.html.twig', array(
	    		'form' => $form->createView(), 'game_session' => $game_session
	    	));
    	}
    }
    
    private function loginGameAllowAccess (Request $request, $game_session_id) {
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	$user_game_session_assotiation = $em->getRepository('GameSessionBundle:UserGameSessionAssociation')
    	->findByUserAndGameSession($this->getUser()->getId(), $game_session_id);
		
    	if ($user_game_session_assotiation) {
    		$user_game_session_assotiation->setAllowAccess(true);
    		$user_game_session_assotiation->setLanguage($request->getLocale());
    		$em->persist($user_game_session_assotiation);
    		$em->flush();
    	}
    	else {
    		$user_game_session_assotiation = new UserGameSessionAssociation();
    		$user_game_session_assotiation->setUserId($this->getUser()->getId());
    		$user_game_session_assotiation->setGameSessionId($game_session_id);
    		$user_game_session_assotiation->setAllowAccess(true);
    		$user_game_session_assotiation->setConnected(false);
    		$user_game_session_assotiation->setLanguage($request->getLocale());
    		$em->persist($user_game_session_assotiation);
    		$em->flush();
    	}
    	return $this->redirectToRoute('join_game_session', array('game_session_id' => $game_session_id));
    }
    
    public function loginGameSessionRejectedAction($game_session_id) {
    	return self::loginGameSessionRejecteRender($game_session_id);
    }
    
    public function loginGameSessionRejecteRender($game_session_id) {
    	$em = $this->getDoctrine()->getManager();
    	$game_session = $em
    		->getRepository('GameSessionBundle:GameSession')
    		->find($game_session_id);
    	
    	return $this->render('GameSessionBundle:Security:re_login_game_session.html.twig', array(
    			'game_session' => $game_session
    	));
    }
    
    public function disconnectedAction ($game_session_id) {
		return self::disconnectedRender($game_session_id);
    }
    
    public function disconnectedRender ($game_session_id) {
    	$em = $this->getDoctrine()->getManager();
    	$game_session = $em
    	->getRepository('GameSessionBundle:GameSession')
    	->find($game_session_id);
    	
    	return $this->render('GameSessionBundle:Security:disconnected_game_session.html.twig', array(
    			'game_session' => $game_session
    	));
    }
    
    public function gameSessionsAction(Request $request) 
    {
    	$game_sessions_actives = $this->getDoctrine()
    	->getRepository('GameSessionBundle:GameSession')
    	->findActiveGameSessions();
    	
    	return self::gameSessionsRender($game_sessions_actives);
    }
    
    public function gameSessionsRender($game_sessions_actives)
    {
    	return $this->render('GameSessionBundle:Web:game_sessions_list.html.twig', array(
    			'game_sessions_actives' => $game_sessions_actives
    	));
    }
    
    public function editGameSessionAction (Request $request, $game_session_id) {
   	 
    	$game_session = $this->getDoctrine()->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
    	
    	$form = $this->createFormBuilder($game_session)
    	->add('name', 'text')
    	->add('password', 'password')
    	->add('comments', 'textarea', array(
        	'required' => false))
//         ->add('submit_button', 'submit')
        ->add('submit_button', SubmitType::class, array(
        		'attr' => array('class' => 'submit'),
        ))
    	->getForm();
    	
//     	$form->handleRequest($request);

    	if ($form->isValid()) {
    		var_dump("patata");die();
			self::updateGameSession($game_session);
//     		$this->addGameSession($game_session);
//     		return $this->redirect($this->generateUrl('join_game_session', array('game_session_id' => $game_session->getId())));
    	}

    	return $this->render('GameSessionBundle:GameSession:edit_popup_game_session.html.twig', array(
    			'form' => $form->createView()
		));
    }
}

<?php
namespace JJSR\Bundle\GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use JJSR\Bundle\GameSessionBundle\Entity\GameSession;
use JJSR\Bundle\GameSessionBundle\Entity\RolGame;

class GameSessionController extends Controller
{
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
    					->findAllActives(),
    	    'choice_translation_domain' => true
			))
    	->add('language', 'entity', array(
    		'required'    => true,
    		'placeholder' => $translator->trans('game_session.create.choose_language'),
		    'class'    => 'GameSessionBundle:Language',
		    'property' => 'name',
    		'choices' => $this->getDoctrine()
    					->getRepository('GameSessionBundle:Language')
    					->findAll(),
    	    'choice_translation_domain' => true
			))
    	->add('comments', 'textarea', array(
        	'required' => false))
        ->add('submit_button', 'submit')
    	->getForm();
    	
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		$this->addGameSession($game_session);
    		return $this->redirect($this->generateUrl('join_game_session', array('game_session_id' => $game_session->getId())));
    	}

    	return $this->render('GameSessionBundle:Web:create.html.twig', array(
    			'form' => $form->createView()
    	));
    }
    
    private function addGameSession($game_session)
    {
        $game_session->setOwner($this->getUser());
        $game_session->setStartDate(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($game_session);
        $em->flush();
    }
    
//     private function gameSessionExist($game_session_id)
//     {
//         $em = $this->getDoctrine()->getManager();
//         return (boolean)$em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
//     }
    
    /*
    public function editGameSessionAction (Request $request, $game_session_id)
    {
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
    */
}

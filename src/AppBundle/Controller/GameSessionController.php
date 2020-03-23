<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\GameSession;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class GameSessionController extends Controller
{
    public function createGameSessionAction(Request $request)
    {
    	$translator = $this->get('translator');

    	$game_session = new GameSession();
    	$game_session->setName("Epic Game");
    	
    	$form = $this->createFormBuilder($game_session, array(
    		'validation_groups' => array('Create')))
    	->add('name')
    	->add('password')
        ->add('rol_game', EntityType::class, array(
            'class' => 'AppBundle:RolGame',
            'required' => true,
            'choices' => $this->getDoctrine()
                ->getRepository('AppBundle:RolGame')
                ->findAllActives(),
            'choice_label' => 'name'
        ))
        ->add('language', EntityType::class, array(
            'class' => 'AppBundle:Language',
            'required' => true,
            'choices' => $this->getDoctrine()
                ->getRepository('AppBundle:Language')
                ->findAll(),
            'choice_label' => 'name'
        ))
    	->add('comments', TextareaType::class, array(
        	'required' => false))
        ->add('submit_button', SubmitType::class)
    	->getForm();

    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		$this->addGameSession($game_session);
    		return $this->redirect($this->generateUrl('login_game_session', array('game_session_id' => $game_session->getId())));
    	}

    	return $this->render('AppBundle:GameSession:add_game_session.html.twig', array(
    			'form' => $form->createView()
    	));
    }
    
    private function addGameSession(GameSession $game_session)
    {
        if ($game_session->getComments() == null) {
            $game_session->setComments('');
        }
        
        $game_session->setOwner($this->getUser());
        $game_session->setStartDate(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($game_session);
        $em->flush();
    }
}

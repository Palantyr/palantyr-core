<?php
namespace JJSR\Bundle\GamingPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdministrationController extends Controller
{
    public function administrationMenuAction(Request $request)
    {
        $data = array();
        $user_game_session_connection_form = $this->createFormBuilder($data)
        ->add(
            'remove_all_user_game_session_connection', 
            'submit',
            array(
                'label' => 'Remove all user game session connection',
                'attr' => array('class' => 'btn btn-danger')
            ))
        ->getForm();

        $user_game_session_connection_form->handleRequest($request);
        
        if ($user_game_session_connection_form->isSubmitted() && $user_game_session_connection_form->isValid()) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            
            if ($user_game_session_connection_form->get('remove_all_user_game_session_connection')->isClicked()) {
                self::removeAllUserGameSessionConnection();
                $flash->success('Operation completed successfully.');
            }
        }
        
        return $this->render('GamingPlatformBundle:Administration:main_menu.html.twig',
            array(
                'user_game_session_connection_form' => $user_game_session_connection_form->createView()
            ));
    }
    
    private function removeAllUserGameSessionConnection()
    {
        $em = $this->getDoctrine()->getEntityManager();
         
        $user_game_session_connections = $this->getDoctrine()
        ->getRepository('GamingPlatformBundle:UserGameSessionConnection')
        ->findAll();
        
        foreach ($user_game_session_connections as $user_game_session_connection) {
            $em->remove($user_game_session_connection);
        }
        $em->flush();
    }
}

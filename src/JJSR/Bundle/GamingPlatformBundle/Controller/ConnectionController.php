<?php
namespace JJSR\Bundle\GamingPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JJSR\Bundle\GameSessionBundle\Entity\GameSession;
use JJSR\Bundle\GamingPlatformBundle\Entity\UserGameSessionConnection;

class ConnectionController extends Controller
{
    public function displayGameSessionsAction(Request $request)
    {
        $game_sessions_actives = $this->getDoctrine()
        ->getRepository('GameSessionBundle:GameSession')
        ->findActiveGameSessions();
         
        return $this->render('GamingPlatformBundle:Web:game_sessions_list.html.twig', array(
            'game_sessions_actives' => $game_sessions_actives
        ));
    }
    
    public function loginGameSessionAction(Request $request, $game_session_id)
    {
        if (!self::gameSessionExist($game_session_id)) {
            return self::gameSessionNotExistAction();
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $game_session = $em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
        if ($game_session->getOwner() == $this->getUser()) {
            self::loginGameSessionAllowAccess($request, $game_session);
            return self::renderGameSessionAction($request, $game_session_id);
        }
        
        $user_game_session_connection = $em->getRepository('GamingPlatformBundle:UserGameSessionConnection')
        ->findOneBy(array('user' => $this->getUser()->getId(), 'game_session' => $game_session_id));
        
        if(!$user_game_session_connection) {
            return self::loginGameSessionRender($request, $game_session_id);
        }
        else {
            switch ($user_game_session_connection->getConnectionOption()) {
                case 'access_denied':
                    return self::loginGameSessionRender($request, $game_session_id);
                    break;
                    
                case 'access_granted':
                    return self::renderGameSessionAction($request, $game_session_id);
                    break;
                    
                case 'connected':
                    return $this->redirectToRoute('login_game_session_rejected', array('game_session_id' => $game_session_id));
                    break;
            }
        }
        var_dump("Aquí no...");die();
    }
    
    private function loginGameSessionRender(Request $request, $game_session_id)
    {
        $em = $this->getDoctrine()->getManager();
        $game_session = $em
        ->getRepository('GameSessionBundle:GameSession')
        ->findCompleteGameSessionById($game_session_id);
         
        $password_game_session = new GameSession();
        $form = $this->createFormBuilder(
            $password_game_session,
            array('validation_groups' => array('Login')))
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
                $game_session = $em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
                self::loginGameSessionAllowAccess($request, $game_session);
                return self::renderGameSessionAction($request, $game_session_id);
            }
        }
        
        return $this->render('GamingPlatformBundle:Security:login_game.html.twig', array(
            'form' => $form->createView(), 'game_session' => $game_session
        ));
    }
    
    private function loginGameSessionAllowAccess (Request $request, $game_session)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user_game_session_connection = $em->getRepository('GamingPlatformBundle:UserGameSessionConnection')
        ->findOneBy(array('user' => $this->getUser(), 'game_session' => $game_session));

        if ($user_game_session_connection) {
            $user_game_session_connection->setConnectionOption('access_granted');
//             $user_game_session_connection->setLanguage($request->getLocale());
            $em->persist($user_game_session_connection);
            $em->flush();
        }
        else {
            $user_game_session_connection = new UserGameSessionConnection();
            $user_game_session_connection->setUser($this->getUser());
            $user_game_session_connection->setGameSession($game_session);
            $user_game_session_connection->setConnectionOption('access_granted');
//             $user_game_session_connection->setLanguage($request->getLocale());
            $em->persist($user_game_session_connection);
            $em->flush();
        }
    }
    
    public function renderGameSessionAction(Request $request, $game_session_id)
    {
        $game_session = $this->getDoctrine()
        ->getRepository('GameSessionBundle:GameSession')
        ->find($game_session_id);
         
        $rol_game = $this->getDoctrine()
        ->getRepository('GameSessionBundle:RolGame')
        ->find($game_session->getRolGame());

        return $this->render('GamingPlatformBundle:GameSession:game_session.html.twig', array(
            'game_session' => $game_session, 'rol_game' => $rol_game
        ));
    }
    
    public function loginGameSessionRejectedAction($game_session_id)
    {
        $em = $this->getDoctrine()->getManager();
        $game_session = $em
        ->getRepository('GameSessionBundle:GameSession')
        ->find($game_session_id);
         
        return $this->render('GamingPlatformBundle:Security:re_login_game_session.html.twig', array(
            'game_session' => $game_session
        ));
    }
    
    public function disconnectedAction ($game_session_id)
    {
        $em = $this->getDoctrine()->getManager();
        $game_session = $em
        ->getRepository('GameSessionBundle:GameSession')
        ->find($game_session_id);
         
        return $this->render('GamingPlatformBundle:Security:disconnected_game_session.html.twig', array(
            'game_session' => $game_session
        ));
    }
    
    public function gameSessionNotExistAction()
    {
        return $this->render('GamingPlatformBundle:Security:game_session_not_exist.html.twig');
    }
    
    private function gameSessionExist($game_session_id)
    {
        $em = $this->getDoctrine()->getManager();
        return (boolean)$em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
    }
}

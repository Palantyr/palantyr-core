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
        $game_sessions = $this->getDoctrine()
        ->getRepository('GameSessionBundle:GameSession')
        ->findAll();
         
        return $this->render('GamingPlatformBundle:Web:game_sessions_list.html.twig', array(
            'game_sessions' => $game_sessions
        ));
    }
    
    public function loginGameSessionAction(Request $request)
    {
        $game_session_id = $request->get('game_session_id');
        
        if (!self::gameSessionExist($game_session_id)) {
            return $this->redirectToRoute('game_session_not_exist', array('game_session_id' => $game_session_id));
        }
        
        $em = $this->getDoctrine()->getManager();
        $game_session = $em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
        $user_game_session_connection = $em->getRepository('GamingPlatformBundle:UserGameSessionConnection')
        ->findOneBy(array('user' => $this->getUser()->getId(), 'game_session' => $game_session_id));
        
        if(isset($user_game_session_connection)) {
            switch ($user_game_session_connection->getConnectionOption()) {
                case 'no_access':
                    if ($game_session->getOwner() == $this->getUser()) {
                        self::loginGameSessionAllowAccess($request, $game_session);
                        return self::gameSessionRender($request);
                    }
                    else {
                        return self::loginGameSessionRender($request);
                    }
                    break;
            
                case 'access_granted':
                    return self::gameSessionRender($request);
                    break;
            
                case 'connected':
                    return $this->redirectToRoute('game_session_already_connected', array('game_session_id' => $game_session_id));
                    break;
            }
        }
        else {
            if ($game_session->getOwner() == $this->getUser()) {
                self::loginGameSessionAllowAccess($request, $game_session);
                return self::gameSessionRender($request);
            }
            else {
                return self::loginGameSessionRender($request);
            }
        }
        
        var_dump("Not here...");die();
    }
    
    private function loginGameSessionRender(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $game_session_id = $request->get('game_session_id');

        $game_session = $em
        ->getRepository('GameSessionBundle:GameSession')
        ->find($game_session_id);
         
        $password_game_session = new GameSession();
        $form = $this->createFormBuilder(
            $password_game_session,
            array('validation_groups' => array('Login')))
        ->add('password', 'password')
        ->add('submit_button', 'submit')
        ->getForm();
             
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            if ($form->getData()->getPassword() != $game_session->getPassword()) {
        
                $this->get('session')->getFlashBag()->add(
                    'bad_login',
                    'game_session.login.incorrect_password'
                    );
            }
             
            else {
                $game_session = $em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
                self::loginGameSessionAllowAccess($request, $game_session);
                return self::gameSessionRender($request);
            }
        }
        
        return $this->render('GamingPlatformBundle:Security:login_game_session.html.twig', array(
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
            $language = $em->getRepository('GameSessionBundle:Language')->findOneBy(array('name' => $request->getLocale()));
            $user_game_session_connection->setLanguage($language);
            $em->persist($user_game_session_connection);
            $em->flush();
        }
        else {
            $user_game_session_connection = new UserGameSessionConnection();
            $user_game_session_connection->setUser($this->getUser());
            $user_game_session_connection->setGameSession($game_session);
            $user_game_session_connection->setConnectionOption('access_granted');
            $language = $em->getRepository('GameSessionBundle:Language')->findOneBy(array('name' => $request->getLocale()));
            $user_game_session_connection->setLanguage($language);
            $em->persist($user_game_session_connection);
            $em->flush();
        }
    }
    
    private function gameSessionRender(Request $request)
    {
        $game_session = $this->getDoctrine()
        ->getRepository('GameSessionBundle:GameSession')
        ->find($request->get('game_session_id'));

        return $this->render('GamingPlatformBundle:GameSession:game_session.html.twig', array(
            'game_session' => $game_session
        ));
    }
    
//     public function loginGameSessionRejectedAction(Request $request)
//     {
//         $em = $this->getDoctrine()->getManager();
//         $game_session = $em
//         ->getRepository('GameSessionBundle:GameSession')
//         ->find($request->get('game_session_id'));
         
//         return $this->render('GamingPlatformBundle:Security:re_login_game_session.html.twig', array(
//             'game_session' => $game_session
//         ));
//     }
    
    public function disconnectedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $game_session = $em
        ->getRepository('GameSessionBundle:GameSession')
        ->find($request->get('game_session_id'));
        
        if (isset($game_session)) {
            return $this->render('GamingPlatformBundle:Security:disconnected_game_session.html.twig', array(
                'game_session' => $game_session
            ));
        }
        else {
            return $this->redirectToRoute('game_session_not_exist', array('game_session_id' => $request->get('game_session_id')));
        }
    }
    
    public function alreadyConnectedAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        $game_session = $em
        ->getRepository('GameSessionBundle:GameSession')
        ->find($request->get('game_session_id'));
        
        if (isset($game_session)) {
            return $this->render('GamingPlatformBundle:Security:already_connected_to_game_session.html.twig', array(
                'game_session' => $game_session
            ));
        }
        else {
            return $this->redirectToRoute('game_session_not_exist', array('game_session_id' => $request->get('game_session_id')));
        }
    }
    
    public function gameSessionNotExistAction(Request $request)
    {
        return $this->render('GamingPlatformBundle:Security:game_session_not_exist.html.twig');
    }
    
    private function gameSessionNotExistRender()
    {
        return $this->render('GamingPlatformBundle:Security:game_session_not_exist.html.twig');
    }
    
    private function gameSessionExist($game_session_id)
    {
        $em = $this->getDoctrine()->getManager();
        return (boolean)$em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
    }
}

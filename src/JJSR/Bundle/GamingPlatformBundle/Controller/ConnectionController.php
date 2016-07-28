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
        ->findBy(array(), array('name' => 'ASC'));

        $defaultData = array();
        $form_search = $this->createFormBuilder($defaultData)
        ->add('search', 'text', array('attr' => array('placeholder' => 'game_session.search.placeholder')))
        ->getForm();
        
        $form_search->handleRequest($request);
        
        if ($form_search->isValid()) {
            $data = $form_search->getData();
            $search = $data['search'];
            if (strlen($search) < 50) {
                $game_sessions = self::searchGameSession($search);
        
                if ($game_sessions) {
                    return $this->render(
                        'GamingPlatformBundle:Web:game_sessions_list.html.twig',
                        array('game_sessions' => $game_sessions, 'form_search' => $form_search->createView())
                        );
                } 
                else {
                    return $this->render(
                        'GamingPlatformBundle:Web:game_sessions_list.html.twig',
                        array(
                            'game_sessions' => 'unsuccessful_search',
                            'form_search' => $form_search->createView(),
                            'search' => $search
                        )
                        );
                }
            }
        }
         
        if ($game_sessions == null || $game_sessions == '') {
            $game_sessions = 'empty';
        }
        return $this->render(
            'GamingPlatformBundle:Web:game_sessions_list.html.twig',
            array('game_sessions' => $game_sessions, 'form_search' => $form_search->createView())
            );
    }
    
    private function searchGameSession($text_to_search)
    {
        $em = $this->getDoctrine()->getManager();
    
        $users = $em->getRepository('UserBundle:User')->createQueryBuilder('o')
        ->where('o.username LIKE :text_to_search')
        ->setParameter('text_to_search','%'.$text_to_search.'%')
        ->getQuery()
        ->getResult();
    
        $game_sessions_aux = array();
        foreach ($users as $user_key => $user_value) {
            $user_id = $user_value->getId();
            $game_sessions_found = $em->getRepository('GameSessionBundle:GameSession')->findBy(array('owner' => $user_id));  
            foreach ($game_sessions_found as $game_session_found_key => $game_session_found_value) {
                $game_sessions_aux[] = $game_session_found_value;
            }
        }
    
        $game_sessions = $em->getRepository('GameSessionBundle:GameSession')->createQueryBuilder('o')
        ->where('o.name LIKE :text_to_search')
        ->orWhere('o.comments LIKE :text_to_search')
        ->setParameter('text_to_search','%'.$text_to_search.'%')
        ->getQuery()
        ->getResult();
        
        foreach ($game_sessions_aux as $game_session_aux_index => $game_session_aux_value) {
            $exist = false;
            foreach ($game_sessions as $game_session_index => $game_session_value) {
                if ($game_session_aux_value->getId() == $game_session_value->getId()) {
                    $exist = true;
                }
            }
            if ($exist == false) {
                $game_sessions[] = $game_session_aux_value;
            }
        }
    
        return $game_sessions;
    }
    
    public function loginGameSessionAction(Request $request)
    {
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        
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
    

    public function deleteGameSessionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
    
        $game_session_id = $request->get('game_session_id');
        $game_session = $em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
    
        if(isset($game_session)) {
            if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN') ||
                $this->getUser() == $game_session->getOwner()) {
    
                    $users_game_session_connection = $em->getRepository('GamingPlatformBundle:UserGameSessionConnection')->findBy(array('game_session' => $game_session));
    
                    $is_allowed_to_delete = true;
                    foreach ($users_game_session_connection as $user_game_session_connection) {
                        if ($user_game_session_connection->getConnectionOption() == 'connected') {
                            $is_allowed_to_delete = false;
                        }
                    }
    
                    if ($is_allowed_to_delete == true) {
                        foreach ($users_game_session_connection as $user_game_session_connection) {
                            $em->remove($user_game_session_connection);
                        }
                        $em->remove($game_session);
                        $em->flush();
                        return $this->redirectToRoute('game_sessions');
                    }
                    else {
                        return $this->redirectToRoute('game_session_already_users_connected', array('game_session_id' => $request->get('game_session_id')));
                    }
                }
            return $this->redirectToRoute('game_session_without_permission', array('game_session_id' => $request->get('game_session_id')));
        }
        return $this->redirectToRoute('game_session_not_exist', array('game_session_id' => $request->get('game_session_id')));
    }
    
    public function gameSessionNotExistAction(Request $request)
    {
        return $this->render('GamingPlatformBundle:Security:game_session_not_exist.html.twig');
    }
    
//     private function gameSessionNotExistRender()
//     {
//         return $this->render('GamingPlatformBundle:Security:game_session_not_exist.html.twig');
//     }

    public function withoutPermissionAction(Request $request) {
        return $this->render('GamingPlatformBundle:Security:without_permission_to_game_session.html.twig');
    }
    
    public function alreadyUsersConnectedAction(Request $request) {
        return $this->render('GamingPlatformBundle:Security:already_users_connected_to_game_session.html.twig');
    }
    
    private function gameSessionExist($game_session_id)
    {
        $em = $this->getDoctrine()->getManager();
        return (boolean)$em->getRepository('GameSessionBundle:GameSession')->find($game_session_id);
    }
}

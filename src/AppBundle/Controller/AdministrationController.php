<?php
namespace AppBundle\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\RolGame;
use AppBundle\Entity\Language;
use AppBundle\Entity\GameSession;
use FOS\UserBundle\Util\UserManipulator as UserManipulator;
use AppBundle\Entity\CharacterSheetTemplate;

class AdministrationController extends Controller
{
    /*
     / DB configuration:
     // a:0:{}
     // a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}
     $userManager = $this->get('fos_user.user_manager');
     $userManipulator = new UserManipulator($userManager);
     $userManipulator->promote($this->getUser()->getUsername());
     $user = $this->getUser()->getRoles();
     var_dump($user);die();
     */
    public function mainMenuAction()
    {
        return $this->render('AppBundle:Administration:main_menu.html.twig');
    }

    public function insufficientPermissionsAction()
    {
        return $this->render('AppBundle:Security:insufficient_permissions.html.twig');
    }

    public function gameSessionMenuAction(Request $request)
    {
        $data = array();
        $default_data_form = $this->createFormBuilder($data)
        ->add(
            'add_all_default_data',
            SubmitType::class,
            array(
                'label' => 'Add all default data',
                'attr' => array('class' => 'btn btn-success')
            ))
        ->add(
            'delete_all_default_data',
            SubmitType::class,
            array(
                'label' => 'Delete all default data',
                'attr' => array('class' => 'btn btn-danger')
            ))
        ->getForm();

        $default_data_form->handleRequest($request);

        if ($default_data_form->isSubmitted() && $default_data_form->isValid()) {
            $flash = $this->get('braincrafted_bootstrap.flash');

            if ($default_data_form->get('add_all_default_data')->isClicked()) {
                if (self::addAllDefaultDataAction() == true) {
                    $flash->success('Operation completed successfully.');
                }
                else {
                    $flash->error('Error. There is some data in the database, remove it first.');
                }
            }

            elseif ($default_data_form->get('delete_all_default_data')->isClicked()) {
                if (self::deleteAllDefaultDataAction() == true) {
                    $flash->success('Operation completed successfully.');
                }
                else {
                    $flash->error('Error. User game session connection in the database, remove it first.');
                }
            }
        }

        return $this->render('AppBundle:Administration:game_session_menu.html.twig',
            array(
                'default_data_form' => $default_data_form->createView()
            ));
    }
    
    public function addAllDefaultDataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $em->getRepository('AppBundle:Language')->findAll();
        $rol_games = $em->getRepository('AppBundle:RolGame')->findAll();
        $character_sheet_templates = $em->getRepository('AppBundle:CharacterSheetTemplate')->findAll();
        if (!$languages && !$rol_games && !$character_sheet_templates) {
            self::addLanguages();
            self::addRolGames();
            self::addCharacterSheetTemplates();
            return true;
        }
        else {
            return false;
        }
    }
    
    public function deleteAllDefaultDataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $em->getRepository('GamingPlatformBundle:UserGameSessionConnection')->findAll();
        if (!$languages) {
            self::deleteGameSessions();
            self::deleteCharacterSheets();
            self::deleteCharacterSheetTemplates();
            self::deleteRolGames();
    		self::deleteLanguages();
            return true;
        }
        else {
            return false;
        }
    }
    
    public function addLanguages()
    {
        $em = $this->getDoctrine()->getManager();
        $language_names = array("es_ES", "en_EN");
        $language_display_names = array("spanish", "english");
        
        for ($count_languages = 0;
        $count_languages < count($language_names);
        $count_languages++) {
        
            $language = new Language();
            $language->setName($language_names[$count_languages]);
            $language->setDisplayName($language_display_names[$count_languages]);
            $em->persist($language);
        }
        $em->flush();
    }
    
    public function addRolGames()
    {
        $em = $this->getDoctrine()->getManager();
        $rol_game_names = array("Pathfinder", "Vampire The Masquerade", "Werewolf", "Star Wars");
        $rol_game_actives = array(1, 1, 0, 0);

        for ($i = 0 ; $i < count($rol_game_names) ; $i++) {
            $rol_game = new RolGame();
            $rol_game->setName($rol_game_names[$i]);
            $rol_game->setActive($rol_game_actives[$i]);
            $em->persist($rol_game);
        }
        $em->flush();
    }
    
    public function addCharacterSheetTemplates()
    {
        $em = $this->getDoctrine()->getManager();
        
        $character_sheet_template = new CharacterSheetTemplate();
        $character_sheet_template->setName('Vampire character');
        $rol_game = $em->getRepository('AppBundle:RolGame')->findOneBy(array('name' => 'Vampire The Masquerade'));
        $character_sheet_template->setRolGame($rol_game);
        $character_sheet_template->setVersion(1);
        $em->persist($character_sheet_template);
        
        $character_sheet_template = new CharacterSheetTemplate();
        $character_sheet_template->setName('Pathfinder character');
        $rol_game = $em->getRepository('AppBundle:RolGame')->findOneBy(array('name' => 'Pathfinder'));
        $character_sheet_template->setRolGame($rol_game);
        $character_sheet_template->setVersion(1);
        $em->persist($character_sheet_template);
        
        $em->flush();
    }
    
    public function deleteLanguages()
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $em->getRepository('AppBundle:Language')->findAll();
        
        foreach ($languages as $language) {
            $em->remove($language);
        }
        $em->flush();
    }
    
    public function deleteRolGames()
    {
        $em = $this->getDoctrine()->getManager();
        $rol_games = $em->getRepository('AppBundle:RolGame')->findAll();

        foreach ($rol_games as $rol_game) {
            $em->remove($rol_game);
        }
        $em->flush();
    }
    
    public function deleteCharacterSheets()
    {
        $em = $this->getDoctrine()->getManager();
        $character_sheets = $em->getRepository('AppBundle:CharacterSheet')->findAll();
        
        foreach ($character_sheets as $character_sheet) {
            $em->remove($character_sheet);
        }
        $em->flush();
    }
    
    public function deleteCharacterSheetTemplates()
    {
        $em = $this->getDoctrine()->getManager();
        $character_sheet_templates = $em->getRepository('AppBundle:CharacterSheetTemplate')->findAll();
    
        foreach ($character_sheet_templates as $character_sheet_template) {
            $em->remove($character_sheet_template);
        }
        $em->flush();
    }
    
    public function deleteGameSessions()
    {
        $em = $this->getDoctrine()->getManager();
        $game_sessions = $em->getRepository('AppBundle:GameSession')->findAll();
    
        foreach ($game_sessions as $game_session) {
            $em->remove($game_session);
        }
        $em->flush();
    }
    
    public function manageCharacterSheetTemplatesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $character_sheet_templates = $em->getRepository('AppBundle:CharacterSheetTemplate')->findAll();
        
        return $this->render('AppBundle:Administration:manage_character_sheet_templates.html.twig', array(
            'character_sheet_templates' => $character_sheet_templates
        ));
    }
    
    public function addCharacterSheetTemplateAction(Request $request)
    {
        $translator = $this->get('translator');
    
        $character_sheet_template = new CharacterSheetTemplate();
    
        $form = $this->createFormBuilder($character_sheet_template, 
            array('validation_groups' => array('Create')))
        ->add('name', TextType::class)
        ->add('version', TextType::class)
        ->add('rol_game', EntityType::class, array(
            'required'    => true,
            'placeholder' => $translator->trans('game_session.create.choose_rol_game'),
            'class'    => 'AppBundle:RolGame',
            'choices' => $this->getDoctrine()
                ->getRepository('AppBundle:RolGame')
                ->findAllActives(),
            'choice_label' => 'name'
        ))
        ->add(
            'submit_button',
            SubmitType::class,
            array(
                'attr' => array('class' => 'btn btn-success')
            ))
        ->getForm();

        $form->handleRequest($request);
         
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($character_sheet_template);
            $em->flush();
            return $this->redirectToRoute('manage_character_sheet_templates');
        }
    
        return $this->render('AppBundle:Administration:add_character_sheet_template.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function removeCharacterSheetTemplateAction(Request $request)
    {
        $character_sheet_template_id = $request->get('character_sheet_template_id');
        $em = $this->getDoctrine()->getManager();
        $character_sheet_template = $em->getRepository('AppBundle:CharacterSheetTemplate')->find($character_sheet_template_id);
        
        if (isset($character_sheet_template)) {
                $em->remove($character_sheet_template);
                $em->flush();
        }

        return $this->redirectToRoute('manage_character_sheet_templates');
    }
    
    public function editCharacterSheetTemplateAction(Request $request)
    {
        $translator = $this->get('translator');
    
        $character_sheet_template_id = $request->get('character_sheet_template_id');
        $em = $this->getDoctrine()->getManager();
        $character_sheet_template = $em->getRepository('AppBundle:CharacterSheetTemplate')->find($character_sheet_template_id);
    
        $form = $this->createFormBuilder($character_sheet_template,
            array('validation_groups' => array('Update')))
        ->add('name', 'text')
        ->add('version', 'text')
        ->add('rol_game', 'entity', array(
            'required'    => true,
            'placeholder' => $translator->trans('game_session.create.choose_rol_game'),
            'class'    => 'AppBundle:RolGame',
            'property' => 'name',
            'choices' => $this->getDoctrine()
            ->getRepository('AppBundle:RolGame')
            ->findAllActives()
        ))
        ->add(
            'submit_button',
            'submit',
            array(
                'attr' => array('class' => 'btn btn-success')
            ))
            ->getForm();
    
        $form->handleRequest($request);
         
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($character_sheet_template);
            $em->flush();
            return $this->redirectToRoute('manage_character_sheet_templates');
        }

        return $this->render('AppBundle:Administration:edit_character_sheet_template.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function gamingPlatformMenuAction(Request $request)
    {
        $data = array();
        $user_game_session_connection_form = $this->createFormBuilder($data)
            ->add(
                'remove_all_user_game_session_connection',
                SubmitType::class,
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

        return $this->render('AppBundle:Administration:gaming_platform_menu.html.twig',
            array(
                'user_game_session_connection_form' => $user_game_session_connection_form->createView()
            ));
    }

    private function removeAllUserGameSessionConnection()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user_game_session_connections = $this->getDoctrine()
            ->getRepository('AppBundle:UserGameSessionConnection')
            ->findAll();

        foreach ($user_game_session_connections as $user_game_session_connection) {
            $em->remove($user_game_session_connection);
        }
        $em->flush();
    }
}

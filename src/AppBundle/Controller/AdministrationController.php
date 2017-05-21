<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    public function administrationMenuAction(Request $request)
    {
        $data = array();
        $default_data_form = $this->createFormBuilder($data)
        ->add(
            'add_all_default_data', 
            'submit', 
            array(
                'label' => 'Add all default data', 
                'attr' => array('class' => 'btn btn-success')
            ))
        ->add(
            'delete_all_default_data', 
            'submit',
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
        
        return $this->render('GameSessionBundle:Administration:main_menu.html.twig',
            array(
                'default_data_form' => $default_data_form->createView()
            ));
    }
    
    public function addAllDefaultDataAction()
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $em->getRepository('GameSessionBundle:Language')->findAll();
        $rol_games = $em->getRepository('GameSessionBundle:RolGame')->findAll();
        $character_sheet_templates = $em->getRepository('GameSessionBundle:CharacterSheetTemplate')->findAll();
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
        $language_names = array("es", "en");
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
        $rol_game = $em->getRepository('GameSessionBundle:RolGame')->findOneBy(array('name' => 'Vampire The Masquerade'));
        $character_sheet_template->setRolGame($rol_game);
        $character_sheet_template->setVersion(1);
        $em->persist($character_sheet_template);
        
        $character_sheet_template = new CharacterSheetTemplate();
        $character_sheet_template->setName('Pathfinder character');
        $rol_game = $em->getRepository('GameSessionBundle:RolGame')->findOneBy(array('name' => 'Pathfinder'));
        $character_sheet_template->setRolGame($rol_game);
        $character_sheet_template->setVersion(1);
        $em->persist($character_sheet_template);
        
        $em->flush();
    }
    
    public function deleteLanguages()
    {
        $em = $this->getDoctrine()->getManager();
        $languages = $em->getRepository('GameSessionBundle:Language')->findAll();
        
        foreach ($languages as $language) {
            $em->remove($language);
        }
        $em->flush();
    }
    
    public function deleteRolGames()
    {
        $em = $this->getDoctrine()->getManager();
        $rol_games = $em->getRepository('GameSessionBundle:RolGame')->findAll();

        foreach ($rol_games as $rol_game) {
            $em->remove($rol_game);
        }
        $em->flush();
    }
    
    public function deleteCharacterSheets()
    {
        $em = $this->getDoctrine()->getManager();
        $character_sheets = $em->getRepository('GameSessionBundle:CharacterSheet')->findAll();
        
        foreach ($character_sheets as $character_sheet) {
            $em->remove($character_sheet);
        }
        $em->flush();
    }
    
    public function deleteCharacterSheetTemplates()
    {
        $em = $this->getDoctrine()->getManager();
        $character_sheet_templates = $em->getRepository('GameSessionBundle:CharacterSheetTemplate')->findAll();
    
        foreach ($character_sheet_templates as $character_sheet_template) {
            $em->remove($character_sheet_template);
        }
        $em->flush();
    }
    
    public function deleteGameSessions()
    {
        $em = $this->getDoctrine()->getManager();
        $game_sessions = $em->getRepository('GameSessionBundle:GameSession')->findAll();
    
        foreach ($game_sessions as $game_session) {
            $em->remove($game_session);
        }
        $em->flush();
    }
    
    public function manageCharacterSheetTemplatesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $character_sheet_templates = $em->getRepository('GameSessionBundle:CharacterSheetTemplate')->findAll();
        
        return $this->render('GameSessionBundle:Administration:manage_character_sheet_templates.html.twig', array(
            'character_sheet_templates' => $character_sheet_templates
        ));
    }
    
    public function addCharacterSheetTemplateAction(Request $request)
    {
        $translator = $this->get('translator');
    
        $character_sheet_template = new CharacterSheetTemplate();
    
        $form = $this->createFormBuilder($character_sheet_template, 
            array('validation_groups' => array('Create')))
        ->add('name', 'text')
        ->add('version', 'text')
        ->add('rol_game', 'entity', array(
            'required'    => true,
            'placeholder' => $translator->trans('game_session.create.choose_rol_game'),
            'class'    => 'GameSessionBundle:RolGame',
            'property' => 'name',
            'choices' => $this->getDoctrine()
            ->getRepository('GameSessionBundle:RolGame')
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
    
        return $this->render('GameSessionBundle:Administration:add_character_sheet_template.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function removeCharacterSheetTemplateAction(Request $request)
    {
        $character_sheet_template_id = $request->get('character_sheet_template_id');
        $em = $this->getDoctrine()->getManager();
        $character_sheet_template = $em->getRepository('GameSessionBundle:CharacterSheetTemplate')->find($character_sheet_template_id);
        
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
        $character_sheet_template = $em->getRepository('GameSessionBundle:CharacterSheetTemplate')->find($character_sheet_template_id);
    
        $form = $this->createFormBuilder($character_sheet_template,
            array('validation_groups' => array('Update')))
        ->add('name', 'text')
        ->add('version', 'text')
        ->add('rol_game', 'entity', array(
            'required'    => true,
            'placeholder' => $translator->trans('game_session.create.choose_rol_game'),
            'class'    => 'GameSessionBundle:RolGame',
            'property' => 'name',
            'choices' => $this->getDoctrine()
            ->getRepository('GameSessionBundle:RolGame')
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

        return $this->render('GameSessionBundle:Administration:edit_character_sheet_template.html.twig', array(
            'form' => $form->createView()
        ));
    }
}

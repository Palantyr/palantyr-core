<?php

namespace GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GameSessionBundle\Entity\GameSession;
use GameSessionBundle\Entity\CharacterSheet;
use GameSessionBundle\Entity\CharacterSheetData;
use GameSessionBundle\Entity\CharacterSheetTemplate;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use GameSessionBundle\Form\Type\CharacterSheetType;
use GameSessionBundle\Form\Listener\AddCharacterSheetTemplateFieldSubscriber;
use Symfony\Component\Form\FormInterface;
use GameSessionBundle\Entity\RolGame;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use GameSessionBundle\Form\Type\AddCharacterSheetType;

class CharacterSheetController extends Controller
{
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');
//         $character_sheet_data = new CharacterSheetData();
//         $character_sheet_data->setName('prueba1');
//         $character_sheet_data->setDisplayName('show prueba');
//         $character_sheet_data->setType('individual');
//         $character_sheet_data->setValue('33');
        
        $character_sheet = new CharacterSheet();
        
        $character_sheet_data1 = new CharacterSheetData();
        $character_sheet_data1->setName('prueba1');
        $character_sheet_data1->setDisplayName('Prueba1');
        $character_sheet_data1->setType('individual');
        $character_sheet_data1->setCharacterSheet($character_sheet);
        $character_sheet->getCharacterSheetData()->add($character_sheet_data1);
        
        $character_sheet_data2 = new CharacterSheetData();
        $character_sheet_data2->setName('prueba2');
        $character_sheet_data2->setDisplayName('Prueba2');
        $character_sheet_data2->setType('colectiva');
        $character_sheet_data2->setCharacterSheet($character_sheet);
        $character_sheet->getCharacterSheetData()->add($character_sheet_data2);
        
        $form = $this->createForm(CharacterSheetType::class, $character_sheet);
        $form->add('character_sheet_template', 'entity', array(
            'required'    => true,
            'placeholder' => $translator->trans('character_sheet.create.choose_template'),
            'class'    => 'GameSessionBundle:CharacterSheetTemplate',
            'property' => 'name',
            'choices' => $this->getDoctrine()
            ->getRepository('GameSessionBundle:CharacterSheetTemplate')
            ->findAll()
        ))
        ->add('submit_button', 'submit');
        
//         $form = $this->createFormBuilder($character_sheet)
//         ->add('name', 'text')
//         ->add('character_sheet_template', 'entity', array(
//             'required'    => true,
//             'placeholder' => $translator->trans('character_sheet.create.choose_template'),
//             'class'    => 'GameSessionBundle:CharacterSheetTemplate',
//             'property' => 'name',
//             'choices' => $this->getDoctrine()
//             ->getRepository('GameSessionBundle:CharacterSheetTemplate')
//             ->findAll()
//         ))
//         ->add('submit_button', 'submit')
//         ->getForm();
        
        $form->handleRequest($request);
         
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
//             $character_sheet_data->setCharacterSheet($character_sheet);
//             $em->persist($character_sheet_data);
            
            $character_sheet->setUser($this->getUser());
//             $character_sheet->addCharacterSheetDatum($character_sheet_data);
            $em->persist($character_sheet);
            $em->flush();
            return $this->redirect($this->generateUrl('character_sheets_homepage'));
        }
        
        return $this->render('GameSessionBundle:CharacterSheet:main_menu.html.twig', array(
    			'form' => $form->createView()
    	));
    }
    
    public function addCharacterSheetMenuAction(Request $request)
    {
        $add_character_sheet_service = $this->get('add_character_sheet_menu_type.service');
        $form = $this->createForm($add_character_sheet_service);         
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $rol_game_url = str_replace(" ", "-", $form->getData()['rol_game']->getName());
            $character_sheet_template_url = str_replace(" ", "-", $form->getData()['character_sheet_template']->getName());
            
            return $this->redirect($this->generateUrl(
                'add_character_sheet',
                array(
                    'rol_game_name' => $rol_game_url, 
                    'character_sheet_template_name' => $character_sheet_template_url)
            ));
        }
        
        return $this->render('GameSessionBundle:CharacterSheet:add_character_sheet_menu.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function addCharacterSheetAction(Request $request)
    {
        $translator = $this->get('translator');
        
        $rol_game_name = str_replace("-", " ", $request->get('rol_game_name'));
        $character_sheet_template_name = str_replace("-", " ", $request->get('character_sheet_template_name'));
        
        $rol_game = $this->getDoctrine()->getRepository('GameSessionBundle:RolGame')->findOneBy(array('name' => $rol_game_name));
        $character_sheet_template = $this->getDoctrine()->getRepository('GameSessionBundle:CharacterSheetTemplate')->findOneBy(array('name' => $character_sheet_template_name));
               
        if (!$rol_game || !$character_sheet_template) {
            
            $error_message = $translator->trans(
                'add_character_sheet.error.message %rol_game_name% %character_sheet_template_name%',
                array(
                    'rol_game_name' => $rol_game_name,
                    'character_sheet_template_name' => $character_sheet_template_name));
            
            return $this->render(
                'GameSessionBundle:CharacterSheet:add_character_sheet_error.html.twig',
                array(
                    'error_message' => $error_message 
            ));
        }
        

        
        $character_sheet = new CharacterSheet();
        $form = $this->createForm(CharacterSheetType::class, $character_sheet);

//         $rol_games_actives = null;
//         $form = $this->createFormBuilder($rol_games_actives)
//             ->add('rol_game', 'entity', array(
//                 'required'    => true,
//                 'placeholder' => $translator->trans('game_session.create.choose_rol_game'),
//                 'class'    => 'GameSessionBundle:RolGame',
//                 'property' => 'name',
//                 'choices' => $this->getDoctrine()
//                 ->getRepository('GameSessionBundle:RolGame')
//                 ->findAllActives()
//             ))
//             ->getForm();
       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//             $em = $this->getDoctrine()->getManager();
//             $em->persist($post);
//             $em->flush();
    
//             return $this->redirect($this->generateUrl(
//                 'admin_post_show',
//                 array('id' => $post->getId())
//             ));
        }
            
        return $this->render('GameSessionBundle:CharacterSheet:add_character_sheet.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    private function vampireCharacterSheet()
    {
        
    }
}
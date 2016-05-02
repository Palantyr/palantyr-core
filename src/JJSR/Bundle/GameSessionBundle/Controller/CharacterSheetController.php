<?php
namespace JJSR\Bundle\GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate;
use JJSR\Bundle\GameSessionBundle\Form\Type\CharacterSheetType;
use JJSR\Bundle\GameSessionBundle\Entity\RolGame;

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
        switch ($rol_game_name) {
            case 'Vampire The Masquerade':
                self::vampireCharacterSheet($character_sheet);
                break;
                
            case 'Pathfinder':
                self::pathfinderCharacterSheet($character_sheet);
                break;
        }
        
//         $character_sheet->getCharacterSheetData()->add($base);
        

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

        $form->add(
            'submit_button',
            'submit',
            array(
                'label' => 'add_character_sheet.continue'
            ));
       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character_sheet->setUser($this->getUser());
            $character_sheet->setCharacterSheetTemplate($character_sheet_template);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($character_sheet);
            $em->flush();
    
//             return $this->redirect($this->generateUrl(
//                 'admin_post_show',
//                 array('id' => $post->getId())
//             ));
        }
            
        return $this->render('GameSessionBundle:CharacterSheet:add_character_sheet.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    private function vampireCharacterSheet(CharacterSheet $character_sheet)
    {
        $main_data = new CharacterSheetData();
        $main_data->setCharacterSheet($character_sheet);
        $main_data->setName('basic_data');
        $main_data->setDatatype('group');
        $character_sheet->addCharacterSheetDatum($main_data);
        
        $character_name = new CharacterSheetData();
//         $character_name->setCharacterSheet($character_sheet);
        $character_name->setName('character_name');
        $character_name->setDatatype('field');
        $character_name->setDisplayName('Character name');
        $character_name->setCharacterSheetDataGroup($main_data);
        $main_data->addCharacterSheetDatum($character_name);
        
        $player_name = new CharacterSheetData();
//         $player_name->setCharacterSheet($character_sheet);
        $player_name->setName('player_name');
        $player_name->setDatatype('field');
        $player_name->setDisplayName('Player name');
        $player_name->setCharacterSheetDataGroup($main_data);
        $main_data->addCharacterSheetDatum($player_name);
        
        $attributes = new CharacterSheetData();
        $attributes->setCharacterSheet($character_sheet);
        $attributes->setName('attributes');
        $attributes->setDatatype('group');
        $attributes->setDisplayName('Attributes');
        $character_sheet->addCharacterSheetDatum($attributes);
        
        $physical = new CharacterSheetData();
//         $physical->setCharacterSheet($character_sheet);
        $physical->setName('physical');
        $physical->setDatatype('group');
        $physical->setDisplayName('Physical');
        $physical->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($physical);
        
        $strength = new CharacterSheetData();
//         $strength->setCharacterSheet($character_sheet);
        $strength->setName('strength');
        $strength->setDatatype('field');
        $strength->setDisplayName('Strength');
        $strength->setCharacterSheetDataGroup($physical);
        $physical->addCharacterSheetDatum($strength);
        
        $dexterity = new CharacterSheetData();
//         $dexterity->setCharacterSheet($character_sheet);
        $dexterity->setName('dexterity');
        $dexterity->setDatatype('field');
        $dexterity->setDisplayName('Dexterity');
        $dexterity->setCharacterSheetDataGroup($physical);
        $physical->addCharacterSheetDatum($dexterity);
    }
    
    private function pathfinderCharacterSheet(CharacterSheet $character_sheet)
    {
        $attributes = new CharacterSheetData();
        $attributes->setCharacterSheet($character_sheet);
        $attributes->setName('attributes');
        $attributes->setDatatype('group');
        $attributes->setDisplayName('Attributes');
        $character_sheet->addCharacterSheetDatum($attributes);
        
        $strengrh_actual = new CharacterSheetData();
        $strengrh_actual->setName('strength_actual');
        $strengrh_actual->setDatatype('group');
        $strengrh_actual->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($strengrh_actual);
        
        $strength_actual_score = new CharacterSheetData();
        $strength_actual_score->setName('strength_actual_value');
        $strength_actual_score->setDatatype('field');
        $strength_actual_score->setDisplayName('Strength actual value');
        $strength_actual_score->setCharacterSheetDataGroup($strengrh_actual);
        $strengrh_actual->addCharacterSheetDatum($strength_actual_score);
        
        
//         $strength_actual_modifier_value = array(
//             'type' => 'integer_division_by_low',
//             'value' => array(
//                 'dividend' => array(
//                     'type' => 'field',
//                     'value' => 'strength_actual_value'),
//                 'divider' => array(
//                     'type' => 'numeric',
//                     'value' => '2'
//                 )
//             )
//         );
//         $strength_actual_modifier_value_json = json_encode($strength_actual_modifier_value);
        
        $strength_actual_modifier = new CharacterSheetData();
        $strength_actual_modifier->setName('strength_actual_modifier');
        $strength_actual_modifier->setDatatype('derived');
        $strength_actual_modifier->setDisplayName('Strength actual Modifier');
//         $strength_actual_modifier->setValue($strength_actual_modifier_value_json);
        $strength_actual_modifier->setCharacterSheetDataGroup($strengrh_actual);
        $strengrh_actual->addCharacterSheetDatum($strength_actual_modifier);
    }
}
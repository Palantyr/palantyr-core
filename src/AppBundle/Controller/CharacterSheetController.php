<?php
namespace AppBundle\Controller;

use AppBundle\Form\Type\AddCharacterSheetMenuType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\CharacterSheet;
use AppBundle\Entity\CharacterSheetData;
use AppBundle\Entity\CharacterSheetTemplate;
use AppBundle\Form\Type\CharacterSheetEditableType;
use AppBundle\Entity\RolGame;
use Symfony\Component\HttpFoundation\JsonResponse;

class CharacterSheetController extends Controller
{  
    public function addCharacterSheetMenuAction(Request $request)
    {
        $add_character_sheet_service = $this->get(AddCharacterSheetMenuType::class);

        $form = $this->createForm(AddCharacterSheetMenuType::class, $add_character_sheet_service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rol_game_url = strtolower(str_replace(" ", "-", $form->get('rol_game')->getData()->getName()));
            $character_sheet_template_url = strtolower(str_replace(" ", "-", $form->get('character_sheet_template')->getData()->getName()));

            return $this->redirect($this->generateUrl(
                'add_character_sheet',
                array(
                    'rol_game_name' => $rol_game_url,
                    'character_sheet_template_name' => $character_sheet_template_url)
            ));
        }

        return $this->render('AppBundle:CharacterSheet:add_character_sheet_menu.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function addCharacterSheetAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return self::handleAjax($request);
        }

        $translator = $this->get('translator');

        $rol_game_name = str_replace("-", " ", $request->get('rol_game_name'));
        $character_sheet_template_name = str_replace("-", " ", $request->get('character_sheet_template_name'));
        
        $rol_game = $this->getDoctrine()->getRepository('AppBundle:RolGame')->findOneBy(array('name' => $rol_game_name));
        $character_sheet_template = $this->getDoctrine()->getRepository('AppBundle:CharacterSheetTemplate')->findOneBy(array('name' => $character_sheet_template_name));
               
        if (!$rol_game || !$character_sheet_template) {
            
            $error_message = $translator->trans(
                'character_sheet.add.error.message %rol_game_name% %character_sheet_template_name%',
                array(
                    'rol_game_name' => $rol_game_name,
                    'character_sheet_template_name' => $character_sheet_template_name
                ));
            
            return $this->render(
                'AppBundle:CharacterSheet:add_character_sheet_error.html.twig',
                array(
                    'error_message' => $error_message
                ));
        }

        $character_sheet = new CharacterSheet();
        switch ($rol_game_name) {
            case 'vampire the masquerade':
                self::vampireCharacterSheet($character_sheet);
                break;

            case 'pathfinder':
                self::pathfinderCharacterSheet($character_sheet);
                break;
        }
        $requestDerivationFields = self::requestDerivationFields($character_sheet_template->getId());

        $form = $this->createForm(CharacterSheetEditableType::class, $character_sheet);

        $form->add(
            'submit_button',
            SubmitType::class,
            array(
                'label' => 'character_sheet.add.create'
            ));
       
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character_sheet->setUser($this->getUser());
            $character_sheet->setCharacterSheetTemplate($character_sheet_template);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($character_sheet);
            $em->flush();
            
            return $this->redirect($this->generateUrl('character_sheets_list'));
        }

        return $this->render('AppBundle:CharacterSheet:add_character_sheet.html.twig', array(
            'form' => $form->createView(),
            'request_derivation_fields' => $requestDerivationFields
        ));
    }
    
    private function getFieldsModificated($id_modified)
    {
        $fields_modificated = array();
        
        if ($id_modified == 'strength_actual_score') {
            $fields_modificated['strength_actual_modifier'] = rand(1, 10);
        }
        elseif ($id_modified == 'strength_temporary_score') {
            $fields_modificated['strength_temporary_modifier'] = rand(1, 10);
        }
        if ($id_modified == 'dexterity_actual_score') {
            $fields_modificated['dexterity_actual_modifier'] = rand(1, 10);
        }
        elseif ($id_modified == 'dexterity_temporary_score') {
            $fields_modificated['dexterity_temporary_modifier'] = rand(1, 10);
        }
        elseif ($id_modified == 'constitution_actual_score') {
            $fields_modificated['constitution_actual_modifier'] = rand(1, 10);
        }
        elseif ($id_modified == 'constitution_temporary_score') {
            $fields_modificated['constitution_temporary_modifier'] = rand(1, 10);
            $fields_modificated['hit_points_total'] = 20 + $fields_modificated['constitution_temporary_modifier'];
            $fields_modificated['hit_points_current'] = $fields_modificated['hit_points_total'];
        }
        
        return $fields_modificated;
    }
    
    private function handleAjax(Request $request)
    {
        $character_sheet_template_name = str_replace("-", " ", $request->get('character_sheet_template_name'));
        $character_sheet_template = $this->getDoctrine()->getRepository('AppBundle:CharacterSheetTemplate')->findOneBy(array('name' => $character_sheet_template_name));

        $character_sheet = self::getCharacterSheetFormAjax($request, $character_sheet_template);
        
        $id_modified = $request->request->get('id_modified');
        $fields_modificated = self::getFieldsModificated($id_modified);

        return new JsonResponse($fields_modificated);
    }
    
    private function getCharacterSheetFormAjax(Request $request, $character_sheet_template)
    {
//        $request = $this->get('request');
        $data = $request->request->all();
        $character_sheet_array = $data['character_sheet_editable'];
        $character_sheet = new CharacterSheet();
        $character_sheet->setCharacterSheetTemplate($character_sheet_template);
        
        foreach ($character_sheet_array['character_sheet_data'] as $character_sheet_data_array) {
            $character_sheet_data = new CharacterSheetData();
            
            $character_sheet_data->setName($character_sheet_data_array['name']);
            $character_sheet_data->setDatatype($character_sheet_data_array['datatype']);
            
            if (isset($character_sheet_data_array['display_name'])) {
                $character_sheet_data->setDisplayName($character_sheet_data_array['display_name']);
            }
            
            switch ($character_sheet_data_array['datatype']) {
            case 'group':
                if (isset($character_sheet_data_array['character_sheet_data'])) {
                    foreach ($character_sheet_data_array['character_sheet_data'] as $character_sheet_data_own_array) {
                        self::getCharacterSheetDataFormAjax($character_sheet_data_own_array, $character_sheet_data);
                    }
                }
                break;
            }
            
            $character_sheet->addCharacterSheetDatum($character_sheet_data);
        }
    }
    
    private function getCharacterSheetDataFormAjax($character_sheet_data_array, $father)
    {
        $character_sheet_data = new CharacterSheetData();
        $father->addCharacterSheetDatum($character_sheet_data);
    
        $character_sheet_data->setName($character_sheet_data_array['name']);
        $character_sheet_data->setDatatype($character_sheet_data_array['datatype']);
        $character_sheet_data->setCharacterSheetDataGroup($father);
    
        if (isset($character_sheet_data_array['display_name'])) {
            $character_sheet_data->setDisplayName($character_sheet_data_array['display_name']);
        }
    
        switch ($character_sheet_data_array['datatype']) {
            case 'group':
                if (isset($character_sheet_data_array['character_sheet_data'])) {
                    foreach ($character_sheet_data_array['character_sheet_data'] as $character_sheet_data_own_array) {
                        self::getCharacterSheetDataFormAjax($character_sheet_data_own_array, $character_sheet_data);
                    }
                }
                break;
    
            case 'field':
                if (isset($character_sheet_data_array['value'])) {
                    $character_sheet_data->setValue($character_sheet_data_array['value']);
                }
                break;
        }
    }
    
    public function deleteCharacterSheetAction(Request $request)
    {
        $character_sheet_id = $request->get('character_sheet_id');
        
        $em = $this->getDoctrine()->getManager();
        $character_sheet = $em->getRepository('AppBundle:CharacterSheet')->find($character_sheet_id);
        
        if (!isset($character_sheet) || $this->getUser() != $character_sheet->getUser()) {
            return $this->redirect($this->generateUrl('insufficient_permissions'));
        }
        
        $em->remove($character_sheet);
        $em->flush();
        
        return $this->redirect($this->generateUrl('character_sheets_list'));
    }
    
    public function characterSheetListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $character_sheets = $em->getRepository('AppBundle:CharacterSheet')->findBy(array('user' => $this->getUser()));
        
        return $this->render('AppBundle:CharacterSheet:character_sheets_list.html.twig', array(
            'character_sheets' => $character_sheets
        ));
    }
    
    public function editCharacterSheetAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            return self::handleAjax($request);
        }
        
        $character_sheet_id = $request->get('character_sheet_id');
        
        $em = $this->getDoctrine()->getManager();
        $character_sheet = $em->getRepository('AppBundle:CharacterSheet')->find($character_sheet_id);

        if (!isset($character_sheet) || $this->getUser() != $character_sheet->getUser()) {
            return $this->redirect($this->generateUrl('insufficient_permissions'));
        }
        
        $form = $this->createForm(CharacterSheetEditableType::class, $character_sheet);

        $form->add(
            'submit_button',
            SubmitType::class,
            array(
                'label' => 'character_sheet.edit.submit'
            ));
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {        
            $em = $this->getDoctrine()->getManager();
            $em->persist($character_sheet);
            $em->flush();
        
            return $this->redirect($this->generateUrl(
                'edit_character_sheet',
                array(
                    'character_sheet_id' => $character_sheet->getId())
                ));
        }

        $requestDerivationFields = self::requestDerivationFields($character_sheet->getCharacterSheetTemplate()->getId());
        
        return $this->render('AppBundle:CharacterSheet:edit_character_sheet.html.twig', array(
            'form' => $form->createView(),
            'request_derivation_fields' => $requestDerivationFields
        ));
    }
    
    private function requestDerivationFields($character_sheet_template_id)
    {
        $requestDerivationFields = array();
        
        $requestDerivationFields[] = 'strength_actual_score';
        $requestDerivationFields[] = 'strength_temporary_score';
        $requestDerivationFields[] = 'dexterity_actual_score';
        $requestDerivationFields[] = 'dexterity_temporary_score';
        $requestDerivationFields[] = 'constitution_actual_score';
        $requestDerivationFields[] = 'constitution_temporary_score';
        
        return $requestDerivationFields;
    }
    
    private function vampireCharacterSheet(CharacterSheet $character_sheet)
    {
        $main_data = new CharacterSheetData();
        $main_data->setCharacterSheet($character_sheet);
        $main_data->setName('main_data');
        $main_data->setDatatype('group');
        $character_sheet->addCharacterSheetDatum($main_data);
        
        $generation = new CharacterSheetData();
        $generation->setName('generation');
        $generation->setDatatype('field');
        $generation->setValidationType('only_integer_numbers');
        $generation->setDisplayName('Generation');
        $generation->setCharacterSheetDataGroup($main_data);
        $main_data->addCharacterSheetDatum($generation);
        
        $attributes = new CharacterSheetData();
        $attributes->setCharacterSheet($character_sheet);
        $attributes->setName('attributes');
        $attributes->setDatatype('group');
        $attributes->setDisplayName('Attributes');
        $character_sheet->addCharacterSheetDatum($attributes);
        
        $physical = new CharacterSheetData();
        $physical->setName('physical');
        $physical->setDatatype('group');
        $physical->setDisplayName('Physical');
        $physical->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($physical);
        
        $strength = new CharacterSheetData();
        $strength->setName('strength');
        $strength->setDatatype('field');
        $strength->setValidationType('only_integer_numbers');
        $strength->setDisplayName('Strength');
        $strength->setCharacterSheetDataGroup($physical);
        $physical->addCharacterSheetDatum($strength);
        
        $dexterity = new CharacterSheetData();
        $dexterity->setName('dexterity');
        $dexterity->setDatatype('field');
        $dexterity->setValidationType('only_integer_numbers');
        $dexterity->setDisplayName('Dexterity');
        $dexterity->setCharacterSheetDataGroup($physical);
        $physical->addCharacterSheetDatum($dexterity);
        
        $abilities = new CharacterSheetData();
        $abilities->setCharacterSheet($character_sheet);
        $abilities->setName('abilities');
        $abilities->setDatatype('group');
        $abilities->setDisplayName('Abilities');
        $character_sheet->addCharacterSheetDatum($abilities);
        
        $talents = new CharacterSheetData();
        $talents->setName('talents');
        $talents->setDatatype('group');
        $talents->setDisplayName('Talents');
        $talents->setCharacterSheetDataGroup($abilities);
        $abilities->addCharacterSheetDatum($talents);
        
        $alert = new CharacterSheetData();
        $alert->setName('alert');
        $alert->setDatatype('field');
        $alert->setValidationType('only_integer_numbers');
        $alert->setDisplayName('Alert');
        $alert->setCharacterSheetDataGroup($talents);
        $talents->addCharacterSheetDatum($alert);
        
        $athletics = new CharacterSheetData();
        $athletics->setName('athletics');
        $athletics->setDatatype('field');
        $athletics->setValidationType('only_integer_numbers');
        $athletics->setDisplayName('Athletics');
        $athletics->setCharacterSheetDataGroup($talents);
        $talents->addCharacterSheetDatum($athletics);
        
        
        $hit_points = new CharacterSheetData();
        $hit_points->setCharacterSheet($character_sheet);
        $hit_points->setName('hit_points');
        $hit_points->setDatatype('group');
        $character_sheet->addCharacterSheetDatum($hit_points);
        
        $hit_points_total = new CharacterSheetData();
        $hit_points_total->setName('hit_points_total');
        $hit_points_total->setDatatype('field');
        $hit_points_total->setValidationType('only_integer_numbers');
        $hit_points_total->setDisplayName('Hit points total');
        $hit_points_total->setCharacterSheetDataGroup($hit_points);
        $hit_points->addCharacterSheetDatum($hit_points_total);
        
        $hit_points_current = new CharacterSheetData();
        $hit_points_current->setName('hit_points_current');
        $hit_points_current->setDatatype('field');
        $hit_points_current->setValidationType('only_integer_numbers');
        $hit_points_current->setDisplayName('Hit points current');
        $hit_points_current->setCharacterSheetDataGroup($hit_points);
        $hit_points->addCharacterSheetDatum($hit_points_current);
    }
    
    private function pathfinderCharacterSheet(CharacterSheet $character_sheet)
    {
        $main_data = new CharacterSheetData();
        $main_data->setCharacterSheet($character_sheet);
        $main_data->setName('main_data');
        $main_data->setDatatype('group');
        $character_sheet->addCharacterSheetDatum($main_data);
        
        $alignment = new CharacterSheetData();
        $alignment->setName('alignment');
        $alignment->setDatatype('field');
        $alignment->setDisplayName('Alignment');
        $alignment->setCharacterSheetDataGroup($main_data);
        $main_data->addCharacterSheetDatum($alignment);
        
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
        $strength_actual_score->setName('strength_actual_score');
        $strength_actual_score->setDatatype('field');
        $strength_actual_score->setValidationType('only_integer_numbers');
        $strength_actual_score->setDisplayName('Strength actual score');
        $strength_actual_score->setCharacterSheetDataGroup($strengrh_actual);
        $strengrh_actual->addCharacterSheetDatum($strength_actual_score);
        
        $strength_actual_modifier = new CharacterSheetData();
        $strength_actual_modifier->setName('strength_actual_modifier');
        $strength_actual_modifier->setDatatype('derived');
        $strength_actual_modifier->setDisplayName('Strength actual modifier');
        $strength_actual_modifier->setCharacterSheetDataGroup($strengrh_actual);
        $strengrh_actual->addCharacterSheetDatum($strength_actual_modifier);
        
        $strength_temporary = new CharacterSheetData();
        $strength_temporary->setName('strength_temporary');
        $strength_temporary->setDatatype('group');
        $strength_temporary->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($strength_temporary);
        
        $strength_temporary_score = new CharacterSheetData();
        $strength_temporary_score->setName('strength_temporary_score');
        $strength_temporary_score->setDatatype('field');
        $strength_temporary_score->setValidationType('only_integer_numbers');
        $strength_temporary_score->setDisplayName('Strength temporary score');
        $strength_temporary_score->setCharacterSheetDataGroup($strength_temporary);
        $strength_temporary->addCharacterSheetDatum($strength_temporary_score);
        
        $strength_temporary_modifier = new CharacterSheetData();
        $strength_temporary_modifier->setName('strength_temporary_modifier');
        $strength_temporary_modifier->setDatatype('derived');
        $strength_temporary_modifier->setDisplayName('Strength temporary modifier');
        $strength_temporary_modifier->setCharacterSheetDataGroup($strength_temporary);
        $strength_temporary->addCharacterSheetDatum($strength_temporary_modifier);

        
        $dexterity_actual = new CharacterSheetData();
        $dexterity_actual->setName('dexterity_actual');
        $dexterity_actual->setDatatype('group');
        $dexterity_actual->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($dexterity_actual);
        
        $dexterity_actual_score = new CharacterSheetData();
        $dexterity_actual_score->setName('dexterity_actual_score');
        $dexterity_actual_score->setDatatype('field');
        $dexterity_actual_score->setValidationType('only_integer_numbers');
        $dexterity_actual_score->setDisplayName('Dexterity actual score');
        $dexterity_actual_score->setCharacterSheetDataGroup($dexterity_actual);
        $dexterity_actual->addCharacterSheetDatum($dexterity_actual_score);
        
        $dexterity_actual_modifier = new CharacterSheetData();
        $dexterity_actual_modifier->setName('dexterity_actual_modifier');
        $dexterity_actual_modifier->setDatatype('derived');
        $dexterity_actual_modifier->setDisplayName('Dexterity actual modifier');
        $dexterity_actual_modifier->setCharacterSheetDataGroup($dexterity_actual);
        $dexterity_actual->addCharacterSheetDatum($dexterity_actual_modifier);
        
        $dexterity_temporary = new CharacterSheetData();
        $dexterity_temporary->setName('dexterity_temporary');
        $dexterity_temporary->setDatatype('group');
        $dexterity_temporary->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($dexterity_temporary);
        
        $dexterity_temporary_score = new CharacterSheetData();
        $dexterity_temporary_score->setName('dexterity_temporary_score');
        $dexterity_temporary_score->setDatatype('field');
        $dexterity_temporary_score->setValidationType('only_integer_numbers');
        $dexterity_temporary_score->setDisplayName('Dexterity temporary score');
        $dexterity_temporary_score->setCharacterSheetDataGroup($dexterity_temporary);
        $dexterity_temporary->addCharacterSheetDatum($dexterity_temporary_score);
        
        $dexterity_temporary_modifier = new CharacterSheetData();
        $dexterity_temporary_modifier->setName('dexterity_temporary_modifier');
        $dexterity_temporary_modifier->setDatatype('derived');
        $dexterity_temporary_modifier->setDisplayName('Dexterity temporary modifier');
        $dexterity_temporary_modifier->setCharacterSheetDataGroup($dexterity_temporary);
        $dexterity_temporary->addCharacterSheetDatum($dexterity_temporary_modifier);
        
        
        $constitution_actual = new CharacterSheetData();
        $constitution_actual->setName('constitution_actual');
        $constitution_actual->setDatatype('group');
        $constitution_actual->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($constitution_actual);
        
        $constitution_actual_score = new CharacterSheetData();
        $constitution_actual_score->setName('constitution_actual_score');
        $constitution_actual_score->setDatatype('field');
        $constitution_actual_score->setValidationType('only_integer_numbers');
        $constitution_actual_score->setDisplayName('Constitution actual score');
        $constitution_actual_score->setCharacterSheetDataGroup($constitution_actual);
        $constitution_actual->addCharacterSheetDatum($constitution_actual_score);
        
        $constitution_actual_modifier = new CharacterSheetData();
        $constitution_actual_modifier->setName('constitution_actual_modifier');
        $constitution_actual_modifier->setDatatype('derived');
        $constitution_actual_modifier->setDisplayName('Constitution actual modifier');
        $constitution_actual_modifier->setCharacterSheetDataGroup($constitution_actual);
        $constitution_actual->addCharacterSheetDatum($constitution_actual_modifier);
        
        $constitution_temporary = new CharacterSheetData();
        $constitution_temporary->setName('constitution_temporary');
        $constitution_temporary->setDatatype('group');
        $constitution_temporary->setCharacterSheetDataGroup($attributes);
        $attributes->addCharacterSheetDatum($constitution_temporary);
        
        $constitution_temporary_score = new CharacterSheetData();
        $constitution_temporary_score->setName('constitution_temporary_score');
        $constitution_temporary_score->setDatatype('field');
        $constitution_temporary_score->setValidationType('only_integer_numbers');
        $constitution_temporary_score->setDisplayName('Constitution temporary score');
        $constitution_temporary_score->setCharacterSheetDataGroup($constitution_temporary);
        $constitution_temporary->addCharacterSheetDatum($constitution_temporary_score);
        
        $constitution_temporary_modifier = new CharacterSheetData();
        $constitution_temporary_modifier->setName('constitution_temporary_modifier');
        $constitution_temporary_modifier->setDatatype('derived');
        $constitution_temporary_modifier->setDisplayName('Constitution temporary modifier');
        $constitution_temporary_modifier->setCharacterSheetDataGroup($constitution_temporary);
        $constitution_temporary->addCharacterSheetDatum($constitution_temporary_modifier);
        
        
        $hit_points = new CharacterSheetData();
        $hit_points->setCharacterSheet($character_sheet);
        $hit_points->setName('hit_points');
        $hit_points->setDatatype('group');
        $character_sheet->addCharacterSheetDatum($hit_points);
        
        $hit_points_total = new CharacterSheetData();
        $hit_points_total->setName('hit_points_total');
        $hit_points_total->setDatatype('derived');
        $hit_points_total->setDisplayName('Hit points total');
        $hit_points_total->setCharacterSheetDataGroup($hit_points);
        $hit_points->addCharacterSheetDatum($hit_points_total);
        
        $hit_points_current = new CharacterSheetData();
        $hit_points_current->setName('hit_points_current');
        $hit_points_current->setDatatype('derived');
        $hit_points_current->setDisplayName('Hit points current');
        $hit_points_current->setCharacterSheetDataGroup($hit_points);
        $hit_points->addCharacterSheetDatum($hit_points_current);
    }
}
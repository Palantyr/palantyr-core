<?php

namespace JJSR\Bundle\GameSessionBundle\Tests\Controller;

use JJSR\Bundle\GameSessionBundle\Entity\GameSession;
use UserBundle\Entity\User;
use JJSR\Bundle\GameSessionBundle\Entity\Language;
use JJSR\Bundle\GameSessionBundle\Entity\RolGame;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData;

class UnitTests extends \PHPUnit_Framework_TestCase
{
    public function testCreateLanguage()
    {
        $language = new Language();
        $language->setName('lt');
        $language->setDisplayName('Language test');
        $this->assertEquals('lt', $language->getName());
        $this->assertEquals('Language test',  $language->getDisplayName());
    }
    
    public function testCreateRolGame()
    {
        $rol_game = new RolGame();
        $rol_game->setName('Rol game test');
        $rol_game->setActive(1);
        $this->assertEquals('Rol game test', $rol_game->getName());
        $this->assertEquals(1,  $rol_game->getActive());
    }
    
    public function testCreateCharacterSheetTemplate()
    {
        $rol_game = new RolGame();
        
        $character_sheet_template = new CharacterSheetTemplate();
        $character_sheet_template->setName('Character sheet template test');
        $character_sheet_template->setRolGame($rol_game);
        $character_sheet_template->setVersion('1');
        $this->assertEquals('Character sheet template test', $character_sheet_template->getName());
        $this->assertEquals($rol_game,  $character_sheet_template->getRolGame());
        $this->assertEquals(1,  $character_sheet_template->getVersion());
    }
    
    public function testCreateCharacterSheet()
    {
        $character_sheet_template = new CharacterSheetTemplate();
        $user = new User();
        
        $character_sheet = new CharacterSheet();
        $character_sheet->setName('Character sheet test');
        $character_sheet->setUser($user);
        $character_sheet->setCharacterSheetTemplate($character_sheet_template);
        $this->assertEquals('Character sheet test', $character_sheet->getName());
        $this->assertEquals($user,  $character_sheet->getUser());
        $this->assertEquals($character_sheet_template,  $character_sheet->getCharacterSheetTemplate());
    }
    
    public function testCreateCharacterSheetData()
    {
        $character_sheet = new CharacterSheet();
        
        $character_sheet_data = new CharacterSheetData();
        $character_sheet_data->setName('character_sheet_data_test');
        $character_sheet_data->setDisplayName('Character sheet data test');
        $character_sheet_data->setCharacterSheet($character_sheet);
        $character_sheet_data->setValue('Value test');
        $character_sheet_data->setDatatype('Datatype test');
        $this->assertEquals('character_sheet_data_test', $character_sheet_data->getName());
        $this->assertEquals('Character sheet data test',  $character_sheet_data->getDisplayName());
        $this->assertEquals($character_sheet,  $character_sheet_data->getCharacterSheet());
        $this->assertEquals('Value test', $character_sheet_data->getValue());
        $this->assertEquals('Datatype test', $character_sheet_data->getDatatype());
    }
    
    public function testCreateGameSession()
    {
        $language = new Language();
        $rol_game = new RolGame();        
        $user = new User();
        
        $game_session = new GameSession();
        $game_session->setName('Game session test');
        $game_session->setOwner($user);
        $game_session->setLanguage($language);
        $game_session->setRolGame($rol_game);
        $game_session->setComments('Comments test');
        $this->assertEquals('Game session test', $game_session->getName());
        $this->assertEquals($user,  $game_session->getOwner());
        $this->assertEquals($language,  $game_session->getLanguage());
        $this->assertEquals($rol_game,  $game_session->getRolGame());
        $this->assertEquals('Comments test',  $game_session->getComments());
    }
}
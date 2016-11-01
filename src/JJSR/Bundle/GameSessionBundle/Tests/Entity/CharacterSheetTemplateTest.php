<?php

namespace JJSR\Bundle\GameSessionBundle\Tests\Entity;

use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate;
use JJSR\Bundle\GameSessionBundle\Entity\RolGame;

class CharacterSheetTemplateTest extends \PHPUnit_Framework_TestCase
{
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
}
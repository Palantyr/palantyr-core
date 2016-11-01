<?php

namespace JJSR\Bundle\GameSessionBundle\Tests\Entity;

use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetTemplate;
use UserBundle\Entity\User;

class CharacterSheetTest extends \PHPUnit_Framework_TestCase
{
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
}
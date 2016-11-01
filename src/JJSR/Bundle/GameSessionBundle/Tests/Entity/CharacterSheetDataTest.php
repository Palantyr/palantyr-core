<?php

namespace JJSR\Bundle\GameSessionBundle\Tests\Entity;

use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheet;
use JJSR\Bundle\GameSessionBundle\Entity\CharacterSheetData;

class CharacterSheetDataTest extends \PHPUnit_Framework_TestCase
{
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
}
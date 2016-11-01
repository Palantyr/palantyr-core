<?php

namespace JJSR\Bundle\GameSessionBundle\Tests\Entity;

use JJSR\Bundle\GameSessionBundle\Entity\RolGame;

class RolGameTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRolGame()
    {
        $rol_game = new RolGame();
        $rol_game->setName('Rol game test');
        $rol_game->setActive(1);
        $this->assertEquals('Rol game test', $rol_game->getName());
        $this->assertEquals(1,  $rol_game->getActive());
    }
}
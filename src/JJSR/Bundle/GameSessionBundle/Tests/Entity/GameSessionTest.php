<?php

namespace JJSR\Bundle\GameSessionBundle\Tests\Entity;

use JJSR\Bundle\GameSessionBundle\Entity\Language;
use JJSR\Bundle\GameSessionBundle\Entity\RolGame;
use JJSR\Bundle\GameSessionBundle\Entity\GameSession;
use UserBundle\Entity\User;

class GameSessionTest extends \PHPUnit_Framework_TestCase
{
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
<?php

namespace JJSR\Bundle\GamingPlatformBundle\Tests\Controller;


use JJSR\Bundle\GamingPlatformBundle\Entity\UserGameSessionConnection;
use UserBundle\Entity\User;
use JJSR\Bundle\GameSessionBundle\Entity\GameSession;
use JJSR\Bundle\GameSessionBundle\Entity\Language;

class UserGameSessionConnectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateUserGameSessionConnection() {
        $user = new User();
        $game_session = new GameSession();
        $language = new Language();
        
        $user_game_session_connection = new UserGameSessionConnection();
        $user_game_session_connection->setUser($user);
        $user_game_session_connection->setGameSession($game_session);
        $user_game_session_connection->setLanguage($language);
        $user_game_session_connection->setConnectionOption('no_access');
        $this->assertEquals($user, $user_game_session_connection->getUser());
        $this->assertEquals($game_session,  $user_game_session_connection->getGameSession());
        $this->assertEquals($language, $user_game_session_connection->getLanguage());
        $this->assertEquals('no_access',  $user_game_session_connection->getConnectionOption());
    }
}
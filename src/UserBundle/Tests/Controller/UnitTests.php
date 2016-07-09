<?php

namespace UserBundle\Tests\Controller;

use UserBundle\Entity\User;

class UnitTests extends \PHPUnit_Framework_TestCase
{
    public function testCreateUser()
    {
        $user = new User();
        $user->setRealName('Real name test');
        $user->setRealSurname('Real surname test');
        $user->setUsername('User test');
        $user->setUsernameCanonical('user test');
        $user->setEmail('email@test.com');
        $user->setEmailCanonical('email@test.com');
        $user->setEnabled(1);
    }
}
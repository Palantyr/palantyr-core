<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    private $client;
    
    /**
     * @dataProvider urlRoleAnonymousProvider
     */
    public function testRoleAnonymousIsSuccessfulWithURLRoleAnonymous($url)
    {
        $this->client = self::createClient();
        $this->client->request('GET', $url);
    
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    /**
     * @dataProvider urlRoleUserProvider
     */
//     public function testRoleAnonymousIsSuccessfulWithRoleUser($url)
//     {
//         $this->client = self::createClient();
//         $this->client->request('GET', $url);
    
//         $this->assertTrue(!$this->client->getResponse()->isSuccessful());
//     }
    
    /**
     * @dataProvider urlRoleAdminProvider
     */
//     public function testRoleAnonymousIsSuccessfulWithRoleAdmin($url)
//     {
//         $this->client = self::createClient();
//         $this->client->request('GET', $url);
    
//         $this->assertTrue(!$this->client->getResponse()->isSuccessful());
//     }
    
    /**
     * @dataProvider urlRoleAnonymousProvider
     */
    public function testRoleUserIsSuccessfulWithURLRoleAnonymous($url)
    {
        $this->client = self::createClient();
        $this->logIn('ROLE_USER');
        $this->client->request('GET', $url);
    
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    /**
     * @dataProvider urlRoleUserProvider
     */
//     public function testRoleUserIsSuccessfulWithURLRoleUser($url)
//     {
//         $this->client = self::createClient();
//         $this->logIn('ROLE_USER');
//         $this->client->request('GET', $url);
    
//         $this->assertTrue($this->client->getResponse()->isSuccessful());
//     }
    
    /**
     * @dataProvider urlRoleAdminProvider
     */
//     public function testRoleUserIsSuccessfulWithURLRoleAdmin($url)
//     {
//         $this->client = self::createClient();
//         $this->logIn('ROLE_USER');
//         $this->client->request('GET', $url);
    
//         $this->assertTrue(!$this->client->getResponse()->isSuccessful());
//     }
    
    /**
     * @dataProvider urlRoleAnonymousProvider
     */
    public function testRoleAdminIsSuccessfulWithURLRoleAnonymous($url)
    {
        $this->client = self::createClient();
        $this->logIn('ROLE_SUPER_ADMIN');
        $this->client->request('GET', $url);
    
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
    
    /**
     * @dataProvider urlRoleUserProvider
     */
//     public function testRoleAdminIsSuccessfulWithURLRoleUser($url)
//     {
//         $this->client = self::createClient();
//         $this->logIn('ROLE_SUPER_ADMIN');
//         $this->client->request('GET', $url);
    
//         $this->assertTrue($this->client->getResponse()->isSuccessful());
//     }
    
    /**
     * @dataProvider urlRoleAdminProvider
     */
//     public function testRoleAdminIsSuccessfulWithURLRoleAdmin($url)
//     {
//         $this->client = self::createClient();
//         $this->logIn('ROLE_SUPER_ADMIN');
//         $this->client->request('GET', $url);
    
//         $this->assertTrue($this->client->getResponse()->isSuccessful());
//     }
    
    
    private function logIn($rol_user)
    {
        $session = $this->client->getContainer()->get('session');
    
        $firewall = 'main';
        $token = new UsernamePasswordToken('admin', null, $firewall, array($rol_user));
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
    
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
    
//     public function urlRoleAdminProvider()
//     {
//         return array(
//             array('')
//         );
//     }
    
//     public function urlRoleUserProvider()
//     {
//         return array(
//             array(''),
//         );
//     }
    
    public function urlRoleAnonymousProvider()
    {
        return array(
            array('/'),
            array('/en'),
            array('/es'),
            array('/en/login'),
            array('/es/login'),
        );
    }
}

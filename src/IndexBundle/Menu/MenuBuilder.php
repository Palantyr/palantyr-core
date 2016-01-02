<?php
namespace IndexBundle\Menu;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav');
		
		$menu->addChild('Game', array('label' => 'Game'))
			->setAttribute('dropdown', true)
			->setAttribute('icon', 'fa fa-user');
		$menu['Game']->addChild('Create Game Session', array('route' => 'create_game_session'))
			->setAttribute('icon', 'fa fa-edit');
		$menu['Game']->addChild('Join Game Session', array('route' => 'game_sessions'))
			->setAttribute('icon', 'fa fa-edit');
		
		$user_permissions = $this->container->get('security.authorization_checker');
		if ($user_permissions->isGranted('ROLE_SUPER_ADMIN')){
			$menu->addChild('Administration', array('route' => 'administration_game_session_homepage'))
				->setAttribute('icon', 'fa fa-list');
		}
        return $menu;
    }
    
    public function userMenu(FactoryInterface $factory, array $options)
    {
    	$user = $this->container->get('security.token_storage')->getToken()->getUser();
    	
    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
 	
		$menu->addChild('User', array('label' => 'Hi '.$user->getUsername().''))
			->setAttribute('dropdown', true)
			->setAttribute('icon', 'fa fa-user');
		$menu['User']->addChild('Edit profile', array('route' => 'app_index'))
			->setAttribute('icon', 'fa fa-edit');
		$menu['User']->addChild('Logout', array('route' => 'fos_user_security_logout'))
			->setAttribute('icon', 'fa fa-edit');
        return $menu;
    }
    
    public function userUnregisteredMainMenu (FactoryInterface $factory, array $options) {
    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav');

    	$menu->addChild('Game', array('label' => 'Game'))
    	->setAttribute('dropdown', true)
    	->setAttribute('icon', 'fa fa-user');
    	$menu['Game']->addChild('Create Game Session', array('route' => 'create_game_session'))
    	->setAttribute('icon', 'fa fa-edit');
    	$menu['Game']->addChild('Join Game Session', array('route' => 'game_sessions'))
    	->setAttribute('icon', 'fa fa-edit');

    	return $menu;
    }
    
    public function userUnregisteredMenu (FactoryInterface $factory, array $options) {
    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
 	
    	$menu->addChild('Sign in', array('route' => 'fos_user_security_login'))
    		->setAttribute('icon', 'fa fa-list');
    	$menu->addChild('Register', array('route' => 'fos_user_registration_register'))
    		->setAttribute('icon', 'fa fa-list');
        return $menu;
    }
}
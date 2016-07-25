<?php
namespace JJSR\Bundle\GamingPlatformBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MenuBuilder extends ContainerAware
{
//     need revision
	public function mainMenu (FactoryInterface $factory, array $options) {
		
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav navbar-nav');
		
		return $menu;
	}
	
	public function secondaryMenu (FactoryInterface $factory, array $options) {
		
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
		
		self::settingsSubMenu($menu);
		
		return $menu;
	}
	
	private function settingsSubMenu ($menu) {
		$translator = $this->container->get('translator');
		
		$menu->addChild('settings',
			array('label' => $translator->trans('secondary_menu.settings.title')))
				->setAttribute('dropdown', true)
				->setAttribute('icon', 'fa fa-user');
		
		$game_session_id = self::getRouteParameter('game_session_id');
		$user = $this->container->get('security.context')->getToken()->getUser();
		if (self::isOwner($user, $game_session_id)) {
			$menu['settings']->addChild($translator->trans('game_session.edit.title'),
					array('uri' => 'javascript:void(0)'))
				->setAttribute('id', 'game_session_edit_button');
			$menu['settings']->addChild($translator->trans('secondary_menu.settings.manage_users.title'),
					array('uri' => 'javascript:void(0)'))
				->setAttribute('id', 'manage_users_button');
		}
		else {
		    $menu['settings']->addChild($translator->trans('secondary_menu.settings.show_users.title'),
		        array('uri' => 'javascript:void(0)'))
		        ->setAttribute('id', 'show_users_button');		    
		}
		
		$menu['settings']->addChild($translator->trans('secondary_menu.settings.exit.title'),
				array('route' => 'game_sessions'))
				->setAttribute('icon', 'fa fa-edit');
		
		return $menu;
	}
	
    public function mainMenu2(FactoryInterface $factory, array $options)
    {
    	$translator = $this->container->get('translator');
    	
    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav');

		self::mainMenuSharedContent($menu);
		
		$user_permissions = $this->container->get('security.authorization_checker');
		if ($user_permissions->isGranted('ROLE_SUPER_ADMIN')){
			$menu->addChild($translator->trans('main_menu.administration'), 
					array('route' => 'administration_game_session_homepage'))
				->setAttribute('icon', 'fa fa-list');
				
		}
        return $menu;
    }
    
    public function userMenu(FactoryInterface $factory, array $options)
    {
    	$translator = $this->container->get('translator');

    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
    	
    	self::languageSubMenu($menu);
   
    	$user = $this->container->get('security.token_storage')->getToken()->getUser();
 	
		$menu->addChild('user',
				array('label' => $translator->trans('secondary_menu.welcome %user_username%', array('%user_username%' => $user->getUsername()))))
			->setAttribute('dropdown', true)
			->setAttribute('icon', 'fa fa-user');
		
		$menu['user']->addChild($translator->trans('secondary_menu.user.view_profile'),
				array('route' => 'fos_user_profile_show'))
			->setAttribute('icon', 'fa fa-edit');
		
		$menu['user']->addChild($translator->trans('secondary_menu.user.logout'),
				array('route' => 'fos_user_security_logout'))
			->setAttribute('icon', 'fa fa-edit');
        return $menu;
    }
    
    public function userUnregisteredMainMenu (FactoryInterface $factory, array $options) {
    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav');

		self::mainMenuSharedContent($menu);

    	return $menu;
    }
    
    public function userUnregisteredMenu (FactoryInterface $factory, array $options) {
    	$translator = $this->container->get('translator');
    	
    	$menu = $factory->createItem('root');
    	$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
    	
    	self::languageSubMenu($menu);
 	
    	$menu->addChild($translator->trans('seconday_menu.sign_in'),
    			array('route' => 'fos_user_security_login'))
    		->setAttribute('icon', 'fa fa-list');
    	
    	$menu->addChild($translator->trans('secondary_menu.register'),
    			array('route' => 'fos_user_registration_register'))
    		->setAttribute('icon', 'fa fa-list');
        return $menu;
    }
    
    private function mainMenuSharedContent ($menu) {
    	$translator = $this->container->get('translator');
    	
    	$menu->addChild('game_session', 
    			array('label' => $translator->trans('main_menu.game_session')))
    		->setAttribute('dropdown', true)
    		->setAttribute('icon', 'fa fa-user');
    	
    	$menu['game_session']->addChild($translator->trans('main_menu.game_session.create'), 
    			array('route' => 'create_game_session'))
    		->setAttribute('icon', 'fa fa-edit');
    	
    	$menu['game_session']->addChild($translator->trans('main_menu.game_session.join'),
    			array('route' => 'game_sessions'))
    		->setAttribute('icon', 'fa fa-edit');
    }
    
    private function languageSubMenu ($menu) {
    	$languages = $this->container->getParameter('app.locales');
    	$current_language = $this->container->get('request')->getLocale();
    	$languages_less_actual = array_values(array_diff($languages, array($current_language)));
    	$current_route = $this->container->get('request')->get('_route');

    	$menu->addChild('Laguage', array('label' => $current_language))
    	->setAttribute('dropdown', true)
    	->setAttribute('icon', 'fa fa-user');
    	
    	$current_parameters = array();
    	$attributes_iterator = $this->container->get('request')->attributes->getIterator();
	    while ($attributes_iterator->valid()) {
	    	if ($attributes_iterator->key() == "_route_params") {
				$current_parameters = $attributes_iterator->current();
	    	}
	    	$attributes_iterator->next();
		}
    	
    	for ($count_languages = 0;
    	$count_languages < count($languages_less_actual);
    	$count_languages++) {
    		
    		if ($current_parameters['_locale']) {
    			$current_parameters['_locale'] = $languages_less_actual[$count_languages];
    		}
    
    		$menu['Laguage']->addChild($languages_less_actual[$count_languages],
    				array('route' => $current_route, 'routeParameters' => $current_parameters))
    				->setAttribute('icon', 'fa fa-edit');
    	}
    }
    
    private function getRouteParameter ($route_parameter) {
    	$attributes_iterator = $this->container->get('request')->attributes->getIterator();
    	while ($attributes_iterator->valid()) {
    		if ($attributes_iterator->key() == "_route_params") {
    			if ($attributes_iterator->current()[$route_parameter]) {
    				return $attributes_iterator->current()[$route_parameter];
    			}
    			else {
    				return null;
    			}
    		}
    		$attributes_iterator->next();
    	}
    }
    
    private function isOwner ($user, $game_session_id) {
    	$game_session = $this->container->get('doctrine')->getRepository('GameSessionBundle:GameSession')->find($game_session_id);

    	if ($game_session->getOwner() == $user) {
    		return true;
    	}
    	return false;
    }
}
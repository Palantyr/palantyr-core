<?php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class GameSessionBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

	public function mainMenu(FactoryInterface $factory, array $options)
    {
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav navbar-nav');
		
		return $menu;
	}
	
	public function secondaryMenu(FactoryInterface $factory, array $options)
    {
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

		self::settingsSubMenu($menu);
		
		return $menu;
	}
	
	private function settingsSubMenu(\Knp\Menu\ItemInterface $menu) {
		$translator = $this->container->get('translator');

		$menu->addChild('settings',
			array('label' => $translator->trans('secondary_menu.settings.title')))
				->setExtra('dropdown', true)
				->setExtra('icon', 'fa fa-user');

		$game_session_id = self::getRouteParameter('game_session_id');
		$user = $this->container->get('security.token_storage')->getToken()->getUser();
		if (self::isOwnerToGameSession($user, $game_session_id)) {
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
    
    private function getRouteParameter ($route_parameter) {
    	$attributes_iterator =$this->container->get('request_stack')->getCurrentRequest()->attributes->getIterator();
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
    
    private function isOwnerToGameSession ($user, $game_session_id) {
    	$game_session = $this->container->get('doctrine')->getRepository('AppBundle:GameSession')->find($game_session_id);

    	if ($game_session->getOwner() == $user) {
    		return true;
    	}
    	return false;
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\DependencyInjection\ContainerAwareInterface::setContainer()
     */
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }
}
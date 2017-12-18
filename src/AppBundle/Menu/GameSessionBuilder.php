<?php
namespace AppBundle\Menu;

use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GameSessionBuilder
{
    private $factory;
    private $requestStack;
    private $translator;
    private $em;
    private $tokenStorage;
    private $container;

    /**
     * Builder constructor.
     * @param FactoryInterface $factory
     * @param RequestStack $requestStack
     * @param Translator $translator
     * @param EntityManager $em
     * @param TokenStorageInterface $tokenStorage
     * @param ContainerInterface $container
     */
    public function __construct(
        FactoryInterface $factory,
        RequestStack $requestStack,
        Translator $translator,
        TokenStorageInterface $tokenStorage,
        ContainerInterface $container,
        EntityManager $em
    ) {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->container = $container;
    }

	public function createGameSessionMainMenu(array $options)
    {
		$menu = $this->factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav navbar-nav');
		
		return $menu;
	}
	
	public function createGameSessionRightMenu(array $options)
    {
		$menu = $this->factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

		self::settingsSubMenu($menu, $options);
		
		return $menu;
	}
	
	private function settingsSubMenu(\Knp\Menu\ItemInterface $menu, array $options)
    {
		$menu
            ->addChild('settings',
			    array('label' => $this->translator->trans('secondary_menu.settings.title')))
            ->setExtra('dropdown', true)
            ->setExtra('icon', 'fa fa-user');

		$gameSessionId = self::getRouteParameter('game_session_id');
		$user = $this->tokenStorage->getToken()->getUser();
		if (self::isOwnerToGameSession($user, $gameSessionId)) {
			$menu['settings']
                ->addChild($this->translator->trans('game_session.edit.title'),
					array('uri' => 'javascript:void(0)'))
				->setAttribute('id', 'game_session_edit_button');
			$menu['settings']
                ->addChild($this->translator->trans('secondary_menu.settings.manage_users.title'),
					array('uri' => 'javascript:void(0)'))
				->setAttribute('id', 'manage_users_button');
		}
		else {
		    $menu['settings']
                ->addChild($this->translator->trans('secondary_menu.settings.show_users.title'),
		            array('uri' => 'javascript:void(0)'))
		        ->setAttribute('id', 'show_users_button');
		}

		$menu['settings']
            ->addChild($this->translator->trans('secondary_menu.settings.exit.title'),
				array('route' => 'game_sessions'))
            ->setAttribute('icon', 'fa fa-edit');

		return $menu;
	}
    
    private function getRouteParameter($routeParameter)
    {
    	$attributesIterator = $this->requestStack->getCurrentRequest()->attributes->getIterator();
    	while ($attributesIterator->valid()) {
    		if ($attributesIterator->key() == "_route_params") {
    			if ($attributesIterator->current()[$routeParameter]) {
    				return $attributesIterator->current()[$routeParameter];
    			}
    			else {
    				return null;
    			}
    		}
            $attributesIterator->next();
    	}
    }
    
    private function isOwnerToGameSession ($user, $gameSessionId)
    {
    	$gameSession = $this->em->getRepository('AppBundle:GameSession')->find($gameSessionId);

    	if ($gameSession->getOwner() == $user) {
    		return true;
    	}
    	return false;
    }
}
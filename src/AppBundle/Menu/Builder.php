<?php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class Builder
{
    private $factory;
    private $requestStack;
    private $translator;
    private $authChecker;
    private $tokenStorage;

    /**
     * Builder constructor.
     * @param FactoryInterface $factory
     * @param RequestStack $requestStack
     * @param Translator $translator
     * @param AuthorizationCheckerInterface $authChecker
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FactoryInterface $factory,
        RequestStack $requestStack,
        Translator $translator,
        AuthorizationCheckerInterface $authChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public function createHomepageMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        self::addHomepageMainMenuContent($menu, $options);

        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            $menu->addChild($this->translator->trans('main_menu.administration'),
                array('route' => 'administration_menu'))
                ->setExtra('icon', 'fa fa-list');

        }
        return $menu;
    }

    public function createHomepageRightMenu(array $options)
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

        self::addLanguageMenu($menu, $options);

        $user = $this->tokenStorage->getToken()->getUser();
        if ($user !== 'anon.') {
            self::addRegisteredUserMenu($menu, $options);
        } else {
            self::addUnregisteredUserMenu($menu, $options);
        }

        return $menu;
    }

    public function addRegisteredUserMenu(\Knp\Menu\ItemInterface $menu, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $menu->addChild('user',
            array('label' => $this->translator->trans(
                'secondary_menu.welcome %user_username%',
                array('%user_username%' => $user->getUsername())
            )))
            ->setExtra('dropdown', true)
            ->setExtra('icon', 'fa fa-user');

        $menu['user']->addChild($this->translator->trans('secondary_menu.user.view_profile'),
            array('route' => 'fos_user_profile_show'))
            ->setExtra('icon', 'fa fa-edit');

        $menu['user']->addChild($this->translator->trans('secondary_menu.character_sheet_list'),
            array('route' => 'character_sheets_list'))
            ->setExtra('icon', 'fa fa-edit');

        $menu['user']->addChild($this->translator->trans('secondary_menu.user.logout'),
            array('route' => 'fos_user_security_logout'))
            ->setExtra('icon', 'fa fa-edit');
    }

    public function addUnregisteredUserMenu(\Knp\Menu\ItemInterface $menu, array $options)
    {
        $menu->addChild($this->translator->trans('secondary_menu.sign_in'),
            array('route' => 'fos_user_security_login'))
            ->setExtra('icon', 'fa fa-list');

        $menu->addChild($this->translator->trans('secondary_menu.register'),
            array('route' => 'fos_user_registration_register'))
            ->setExtra('icon', 'fa fa-list');
    }

    private function addHomepageMainMenuContent(\Knp\Menu\ItemInterface $menu, array $options)
    {
        $menu->addChild('game_session',
            array('label' => $this->translator->trans('main_menu.game_session.title')))
            ->setExtra('dropdown', true)
            ->setExtra('icon', 'fa fa-user');

        $menu['game_session']->addChild($this->translator->trans('main_menu.game_session.create'),
            array('route' => 'create_game_session'))
            ->setExtra('icon', 'fa fa-edit');

        $menu['game_session']->addChild($this->translator->trans('main_menu.game_session.join'),
            array('route' => 'game_sessions'))
            ->setExtra('icon', 'fa fa-edit');

        $menu->addChild($this->translator->trans('main_menu.add_character_sheet.title'),
            array('route' => 'add_character_sheet_menu'))
            ->setExtra('icon', 'fa fa-list');
    }

    private function addLanguageMenu(\Knp\Menu\ItemInterface $menu, array $options)
    {
        $currentLanguage = $this->requestStack->getCurrentRequest()->getLocale();
        $languages = $this->requestStack->getCurrentRequest()->getLanguages();

        $languagesExceptCurrentOne = array_values(array_diff($languages, array($currentLanguage)));
        $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');

        $menu->addChild('Laguage', array('label' => $currentLanguage))
            ->setExtra('dropdown', true)
            ->setExtra('icon', 'fa fa-user');

        $currentParameters = array();
        $attributesIterator = $this->requestStack->getCurrentRequest()->attributes->getIterator();
        while ($attributesIterator->valid()) {
            if ($attributesIterator->key() == "_route_params") {
                $currentParameters = $attributesIterator->current();
            }
            $attributesIterator->next();
        }

        for ($countLanguages = 0;
             $countLanguages < count($languagesExceptCurrentOne);
             $countLanguages++) {

            if ($currentParameters['_locale']) {
                $currentParameters['_locale'] = $languagesExceptCurrentOne[$countLanguages];
            }

            $menu['Laguage']->addChild($languagesExceptCurrentOne[$countLanguages],
                array('route' => $currentRoute, 'routeParameters' => $currentParameters))
                ->setExtra('icon', 'fa fa-edit');
        }
    }

}
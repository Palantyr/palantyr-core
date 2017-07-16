<?php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $translator = $this->container->get('translator');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        self::mainMenuSharedContent($menu);

        $user_permissions = $this->container->get('security.authorization_checker');
        if ($user_permissions->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild($translator->trans('main_menu.administration'),
                array('route' => 'administration_menu'))
                ->setExtra('icon', 'fa fa-list');

        }
        return $menu;
    }

    public function userMenu(FactoryInterface $factory, array $options)
    {
        $translator = $this->container->get('translator');

        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');

//        self::languageSubMenu($menu);

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $menu->addChild('user',
            array('label' => $translator->trans('secondary_menu.welcome %user_username%', array('%user_username%' => $user->getUsername()))))
            ->setExtra('dropdown', true)
            ->setExtra('icon', 'fa fa-user');

        $menu['user']->addChild($translator->trans('secondary_menu.user.view_profile'),
            array('route' => 'fos_user_profile_show'))
            ->setExtra('icon', 'fa fa-edit');

        $menu['user']->addChild($translator->trans('secondary_menu.character_sheet_list'),
            array('route' => 'character_sheets_list'))
            ->setExtra('icon', 'fa fa-edit');

        $menu['user']->addChild($translator->trans('secondary_menu.user.logout'),
            array('route' => 'fos_user_security_logout'))
            ->setExtra('icon', 'fa fa-edit');
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

//        self::languageSubMenu($menu);

        $menu->addChild($translator->trans('secondary_menu.sign_in'),
            array('route' => 'fos_user_security_login'))
            ->setExtra('icon', 'fa fa-list');

        $menu->addChild($translator->trans('secondary_menu.register'),
            array('route' => 'fos_user_registration_register'))
            ->setExtra('icon', 'fa fa-list');
        return $menu;
    }

    private function mainMenuSharedContent ($menu) {
        $translator = $this->container->get('translator');

        $menu->addChild('game_session',
            array('label' => $translator->trans('main_menu.game_session.title')))
            ->setExtra('dropdown', true)
            ->setExtra('icon', 'fa fa-user');

        $menu['game_session']->addChild($translator->trans('main_menu.game_session.create'),
            array('route' => 'create_game_session'))
            ->setExtra('icon', 'fa fa-edit');

        $menu['game_session']->addChild($translator->trans('main_menu.game_session.join'),
            array('route' => 'game_sessions'))
            ->setExtra('icon', 'fa fa-edit');

        $menu->addChild($translator->trans('main_menu.add_character_sheet.title'),
            array('route' => 'add_character_sheet_menu'))
            ->setExtra('icon', 'fa fa-list');
    }

    private function languageSubMenu ($menu) {
        $languages = $this->container->getParameter('app.locales');
        $current_language = $this->container->get('request')->getLocale();
        $languages_less_actual = array_values(array_diff($languages, array($current_language)));
        $current_route = $this->container->get('request')->get('_route');

        $menu->addChild('Laguage', array('label' => $current_language))
            ->setExtra('dropdown', true)
            ->setExtra('icon', 'fa fa-user');

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
                ->setExtra('icon', 'fa fa-edit');
        }
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\DependencyInjection\ContainerAwareInterface::setContainer()
     */
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }

}
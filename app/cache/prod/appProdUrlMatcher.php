<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * appProdUrlMatcher.
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class appProdUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        if (0 === strpos($pathinfo, '/game')) {
            // game_session_homepage
            if ($pathinfo === '/game') {
                return array (  '_controller' => 'GameSessionBundle\\Controller\\MainController::indexAction',  '_route' => 'game_session_homepage',);
            }

            // create_game_session
            if ($pathinfo === '/game/createGameSesion') {
                return array (  '_controller' => 'GameSessionBundle\\Controller\\MainController::createGameSesionAction',  '_route' => 'create_game_session',);
            }

            // session_started
            if ($pathinfo === '/game/sessionStarted') {
                return array (  '_controller' => 'GameSessionBundle\\Controller\\MainController::sessionStartedAction',  '_route' => 'session_started',);
            }

        }

        if (0 === strpos($pathinfo, '/l')) {
            if (0 === strpos($pathinfo, '/log')) {
                if (0 === strpos($pathinfo, '/login')) {
                    // login
                    if ($pathinfo === '/login') {
                        return array (  '_controller' => 'UserBundle\\Controller\\SecurityController::loginAction',  '_route' => 'login',);
                    }

                    // login_check
                    if ($pathinfo === '/login_check') {
                        return array('_route' => 'login_check');
                    }

                }

                // logout
                if ($pathinfo === '/logout') {
                    return array('_route' => 'logout');
                }

            }

            // lucky_number
            if (0 === strpos($pathinfo, '/lucky/number') && preg_match('#^/lucky/number/(?P<count>[^/]++)$#s', $pathinfo, $matches)) {
                return $this->mergeDefaults(array_replace($matches, array('_route' => 'lucky_number')), array (  '_controller' => 'IndexBundle\\Controller\\LuckyController::numberAction',));
            }

        }

        // api_lucky_number
        if (rtrim($pathinfo, '/') === '/api/lucky/number') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'api_lucky_number');
            }

            return array (  '_controller' => 'IndexBundle\\Controller\\LuckyController::apiNumberAction',  '_route' => 'api_lucky_number',);
        }

        // app_index
        if (rtrim($pathinfo, '/') === '') {
            if (substr($pathinfo, -1) !== '/') {
                return $this->redirect($pathinfo.'/', 'app_index');
            }

            return array (  '_controller' => 'AppBundle\\Controller\\DefaultController::indexAction',  '_route' => 'app_index',);
        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}

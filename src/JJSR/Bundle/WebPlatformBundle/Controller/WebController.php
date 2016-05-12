<?php
namespace JJSR\Bundle\WebPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WebController extends Controller
{
    public function homepageAction()
    {
        return $this->render('WebPlatformBundle:Web:homepage.html.twig');
    }
    
    public function insufficientPermissionsAction()
    {
        return $this->render('WebPlatformBundle:Security:insufficient_permissions.html.twig');
    }
}

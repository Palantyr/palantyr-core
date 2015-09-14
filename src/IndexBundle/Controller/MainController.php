<?php

namespace IndexBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\ButtonBuilder;

class MainController extends Controller
{
    public function indexAction()
    {
        return $this->render('IndexBundle:Index:index.html.twig');
    }
}

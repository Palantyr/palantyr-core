<?php
// src/IndexBundle/Controller/IndexController.php

namespace RulesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use RulesBundle\Controller\SistemaReglas\SistemaReglas;

class formExecutor{
    public $game;
    public $pj1;
    public $pj2;
    public $action;
    public $spell;
    public $CD;
    public $skill;
}

class RulesController extends Controller{
    /**
     * @Route("/reglas")
     */
    public function indexAction(Request $request){

        $executor = new formExecutor();

        $var = $this->createFormBuilder($executor)
            ->add('game','choice', array('choices' => array("D&D3.5" => 'D&D35'), 'required' => true))
            ->add('pj1','choice', array('choices' => array("1" => 1, "2"=> 2)))
            ->add('pj2','choice', array('choices' => array("1" => 1, "2"=> 2), 'required' => false))
            ->add('action','choice', array('choices' => array("Ataque" => 'Ataque', "HechizoObjetivo" => 'HechizoObjetivo', "AutoHechizo" => 'AutoHechizo', "TiradaDificultad" => 'TiradaDificultad', "TiradaEnfrentada" => 'TiradaEnfrentada'), 'required' => true))
            ->add('spell','choice', array('choices' => array("Comprension Idiomatica" => 'Comprension Idiomatica', "Manos Ardientes" => 'Manos Ardientes', "Bola de Fuego" => 'Bola de Fuego'), 'required' => false, 'data' => ''))
            ->add('CD','integer', array('required' => false, 'data' => 0))
            ->add('skill','choice', array('choices' => array("Robar" => 'Robar', "Nadar" => 'Nadar', "Mentir" => 'Mentir'),'required' => false, 'data' => ''))
            ->getForm();


        $html = $this->container->get('templating')->render(
            'rules/rules.html.twig', array('form' => $var->createView(), 'hola' => '')
        );

        if ($request->isMethod('POST')) {
        $var->bind($request);

            if ($var->isValid()) {
                $SistemaReglas = new SistemaReglas();
                $hola = $SistemaReglas->ejecutorReglas($executor);
                $html = $html = $this->container->get('templating')->render(
                    'rules/rules.html.twig', array('form' => $var->createView(), 'hola' => $hola)
                 );
                return new Response($html);
            }
        }

        return new Response($html);
    }
}
?> 
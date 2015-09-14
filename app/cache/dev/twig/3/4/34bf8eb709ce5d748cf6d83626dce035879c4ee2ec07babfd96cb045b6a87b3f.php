<?php

/* GameSessionBundle:GameSession:gameSession.html.twig */
class __TwigTemplate_34bf8eb709ce5d748cf6d83626dce035879c4ee2ec07babfd96cb045b6a87b3f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "GameSessionBundle:GameSession:gameSession.html.twig", 1);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_7bf40332ecec28da792b03a745c37f34149f6eb59bb7c6ac0ec59f8d6a29c83b = $this->env->getExtension("native_profiler");
        $__internal_7bf40332ecec28da792b03a745c37f34149f6eb59bb7c6ac0ec59f8d6a29c83b->enter($__internal_7bf40332ecec28da792b03a745c37f34149f6eb59bb7c6ac0ec59f8d6a29c83b_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "GameSessionBundle:GameSession:gameSession.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_7bf40332ecec28da792b03a745c37f34149f6eb59bb7c6ac0ec59f8d6a29c83b->leave($__internal_7bf40332ecec28da792b03a745c37f34149f6eb59bb7c6ac0ec59f8d6a29c83b_prof);

    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        $__internal_f06e139b899e03ace163716ba3a37a187a8539d44414ec83ef36a6fe2eff3352 = $this->env->getExtension("native_profiler");
        $__internal_f06e139b899e03ace163716ba3a37a187a8539d44414ec83ef36a6fe2eff3352->enter($__internal_f06e139b899e03ace163716ba3a37a187a8539d44414ec83ef36a6fe2eff3352_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "Hello to GAME

";
        // line 6
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["game_session"]) ? $context["game_session"] : $this->getContext($context, "game_session")), "getName", array(), "method"), "html", null, true);
        echo "
";
        
        $__internal_f06e139b899e03ace163716ba3a37a187a8539d44414ec83ef36a6fe2eff3352->leave($__internal_f06e139b899e03ace163716ba3a37a187a8539d44414ec83ef36a6fe2eff3352_prof);

    }

    public function getTemplateName()
    {
        return "GameSessionBundle:GameSession:gameSession.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  44 => 6,  40 => 4,  34 => 3,  11 => 1,);
    }
}

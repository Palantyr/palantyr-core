<?php

/* GameSessionBundle:GameSession:gameSessions.html.twig */
class __TwigTemplate_9e69ede6fdc5100fc03a7d45654156699b9aafd0d016f5838b2b18e80fd34fd4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "GameSessionBundle:GameSession:gameSessions.html.twig", 1);
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
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "Hello to game sessions
</br>
<a href=\"";
        // line 6
        echo $this->env->getExtension('routing')->getPath("create_game_session");
        echo "\">Create Game Sesion</a>
</br>
<a href=\"";
        // line 8
        echo $this->env->getExtension('routing')->getPath("game_session_homepage");
        echo "\">Join Game Sesion</a>
";
    }

    public function getTemplateName()
    {
        return "GameSessionBundle:GameSession:gameSessions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  40 => 8,  35 => 6,  31 => 4,  28 => 3,  11 => 1,);
    }
}

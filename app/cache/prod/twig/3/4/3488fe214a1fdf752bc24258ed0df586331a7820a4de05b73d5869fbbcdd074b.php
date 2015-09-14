<?php

/* IndexBundle:Index:index.html.twig */
class __TwigTemplate_3488fe214a1fdf752bc24258ed0df586331a7820a4de05b73d5869fbbcdd074b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "IndexBundle:Index:index.html.twig", 1);
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
        echo "    <h1>Welcome to Index</h1>
\t<a href=\"";
        // line 5
        echo $this->env->getExtension('routing')->getPath("game_session_homepage");
        echo "\">Go to Game</a>
";
    }

    public function getTemplateName()
    {
        return "IndexBundle:Index:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  34 => 5,  31 => 4,  28 => 3,  11 => 1,);
    }
}

<?php

/* GameSessionBundle:GameSession:createGameSesion.html.twig */
class __TwigTemplate_622649f05db5ad6df7b8de58c22987aa69e3734df03819aeb8b9f13e37cccbcd extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "GameSessionBundle:GameSession:createGameSesion.html.twig", 1);
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
        $__internal_0ce87eaddb8429b6fde474612c375fccf3206d3766892b4101e15b520f81ef3e = $this->env->getExtension("native_profiler");
        $__internal_0ce87eaddb8429b6fde474612c375fccf3206d3766892b4101e15b520f81ef3e->enter($__internal_0ce87eaddb8429b6fde474612c375fccf3206d3766892b4101e15b520f81ef3e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "GameSessionBundle:GameSession:createGameSesion.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_0ce87eaddb8429b6fde474612c375fccf3206d3766892b4101e15b520f81ef3e->leave($__internal_0ce87eaddb8429b6fde474612c375fccf3206d3766892b4101e15b520f81ef3e_prof);

    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        $__internal_9d65c9feb983e2ed7cf29862113d1e6a9482ca23a029b175992818435f73cea8 = $this->env->getExtension("native_profiler");
        $__internal_9d65c9feb983e2ed7cf29862113d1e6a9482ca23a029b175992818435f73cea8->enter($__internal_9d65c9feb983e2ed7cf29862113d1e6a9482ca23a029b175992818435f73cea8_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "<h1>Create Game Sesion</h1>

";
        // line 6
        echo         $this->env->getExtension('form')->renderer->renderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'form');
        echo "

</br>

<form action=\"";
        // line 10
        echo $this->env->getExtension('routing')->getPath("game_session_homepage");
        echo "\">
\t<input type=\"submit\" value=\"Cancel\">
</form>
";
        
        $__internal_9d65c9feb983e2ed7cf29862113d1e6a9482ca23a029b175992818435f73cea8->leave($__internal_9d65c9feb983e2ed7cf29862113d1e6a9482ca23a029b175992818435f73cea8_prof);

    }

    public function getTemplateName()
    {
        return "GameSessionBundle:GameSession:createGameSesion.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 10,  44 => 6,  40 => 4,  34 => 3,  11 => 1,);
    }
}

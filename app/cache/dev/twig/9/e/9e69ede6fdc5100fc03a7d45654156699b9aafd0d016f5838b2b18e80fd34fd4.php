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
        $__internal_351b2425b5810814b9b4543c210550014f59186708b6bb37e34cc46f83b16445 = $this->env->getExtension("native_profiler");
        $__internal_351b2425b5810814b9b4543c210550014f59186708b6bb37e34cc46f83b16445->enter($__internal_351b2425b5810814b9b4543c210550014f59186708b6bb37e34cc46f83b16445_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "GameSessionBundle:GameSession:gameSessions.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_351b2425b5810814b9b4543c210550014f59186708b6bb37e34cc46f83b16445->leave($__internal_351b2425b5810814b9b4543c210550014f59186708b6bb37e34cc46f83b16445_prof);

    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        $__internal_aa315bb14a8684434016938cf8fe84232b4e649af5316462206f7e50574c0130 = $this->env->getExtension("native_profiler");
        $__internal_aa315bb14a8684434016938cf8fe84232b4e649af5316462206f7e50574c0130->enter($__internal_aa315bb14a8684434016938cf8fe84232b4e649af5316462206f7e50574c0130_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "<h1>Game index</h1>
</br>
<form action=\"";
        // line 6
        echo $this->env->getExtension('routing')->getPath("create_game_session");
        echo "\">
\t<input type=\"submit\" value=\"Create Game Sesion\">
</form>
</br>
<form action=\"";
        // line 10
        echo $this->env->getExtension('routing')->getPath("game_sessions");
        echo "\">
\t<input type=\"submit\" value=\"Join Game Sesion\">
</form>
</br>
<form action=\"";
        // line 14
        echo $this->env->getExtension('routing')->getPath("app_index");
        echo "\">
\t<input type=\"submit\" value=\"Return\">
</form>
";
        
        $__internal_aa315bb14a8684434016938cf8fe84232b4e649af5316462206f7e50574c0130->leave($__internal_aa315bb14a8684434016938cf8fe84232b4e649af5316462206f7e50574c0130_prof);

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
        return array (  58 => 14,  51 => 10,  44 => 6,  40 => 4,  34 => 3,  11 => 1,);
    }
}

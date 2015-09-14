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
        $__internal_bd9aee6fea76ceda1c953b44f7c21dc87acd653ce78dc7775c5859865f7b80fd = $this->env->getExtension("native_profiler");
        $__internal_bd9aee6fea76ceda1c953b44f7c21dc87acd653ce78dc7775c5859865f7b80fd->enter($__internal_bd9aee6fea76ceda1c953b44f7c21dc87acd653ce78dc7775c5859865f7b80fd_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "IndexBundle:Index:index.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_bd9aee6fea76ceda1c953b44f7c21dc87acd653ce78dc7775c5859865f7b80fd->leave($__internal_bd9aee6fea76ceda1c953b44f7c21dc87acd653ce78dc7775c5859865f7b80fd_prof);

    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        $__internal_1c6506604093664dc532bf8b198ee13f81ad0e2e8d5de802390c91b181b1aece = $this->env->getExtension("native_profiler");
        $__internal_1c6506604093664dc532bf8b198ee13f81ad0e2e8d5de802390c91b181b1aece->enter($__internal_1c6506604093664dc532bf8b198ee13f81ad0e2e8d5de802390c91b181b1aece_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "    <h1>Welcome to Palantir project,
    ";
        // line 5
        if (($this->getAttribute((isset($context["app"]) ? $context["app"] : $this->getContext($context, "app")), "user", array()) == true)) {
            // line 6
            echo "    \t";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : $this->getContext($context, "app")), "user", array()), "username", array()), "html", null, true);
            echo "
\t";
        }
        // line 8
        echo "    </h1>
    
    <br>
    
    ";
        // line 12
        if (($this->getAttribute((isset($context["app"]) ? $context["app"] : $this->getContext($context, "app")), "user", array()) == false)) {
            // line 13
            echo "        <form action=\"";
            echo $this->env->getExtension('routing')->getPath("account_register");
            echo "\">
\t\t\t<input type=\"submit\" value=\"Register\">
\t\t</form>
\t\t
\t    <br>
\t    
\t    <form action=\"";
            // line 19
            echo $this->env->getExtension('routing')->getPath("login");
            echo "\">
\t\t\t<input type=\"submit\" value=\"Login\">
\t\t</form>
\t";
        } else {
            // line 23
            echo "    \t<form action=\"";
            echo $this->env->getExtension('routing')->getPath("logout");
            echo "\">
\t\t\t<input type=\"submit\" value=\"Logout\">
\t\t</form>
\t";
        }
        // line 27
        echo "\t
    <br>
    
    <form action=\"";
        // line 30
        echo $this->env->getExtension('routing')->getPath("game_session_homepage");
        echo "\">
\t\t<input type=\"submit\" value=\"Game\">
\t</form>
";
        
        $__internal_1c6506604093664dc532bf8b198ee13f81ad0e2e8d5de802390c91b181b1aece->leave($__internal_1c6506604093664dc532bf8b198ee13f81ad0e2e8d5de802390c91b181b1aece_prof);

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
        return array (  89 => 30,  84 => 27,  76 => 23,  69 => 19,  59 => 13,  57 => 12,  51 => 8,  45 => 6,  43 => 5,  40 => 4,  34 => 3,  11 => 1,);
    }
}

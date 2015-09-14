<?php

/* GameSessionBundle:GameSession:gameSessionsList.html.twig */
class __TwigTemplate_140685d0badce8f75d9fa65eee543f510a3fa8a956a7626d3f703833820f8649 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("base.html.twig", "GameSessionBundle:GameSession:gameSessionsList.html.twig", 1);
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
        $__internal_8997a767f546dd23d766da126d494570cbc3795392848e0dc0e90d338396b923 = $this->env->getExtension("native_profiler");
        $__internal_8997a767f546dd23d766da126d494570cbc3795392848e0dc0e90d338396b923->enter($__internal_8997a767f546dd23d766da126d494570cbc3795392848e0dc0e90d338396b923_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "GameSessionBundle:GameSession:gameSessionsList.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_8997a767f546dd23d766da126d494570cbc3795392848e0dc0e90d338396b923->leave($__internal_8997a767f546dd23d766da126d494570cbc3795392848e0dc0e90d338396b923_prof);

    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        $__internal_0d2c8af5d51e075b1e6dd5a9d22d0297db66ebe288955534193519565cb72190 = $this->env->getExtension("native_profiler");
        $__internal_0d2c8af5d51e075b1e6dd5a9d22d0297db66ebe288955534193519565cb72190->enter($__internal_0d2c8af5d51e075b1e6dd5a9d22d0297db66ebe288955534193519565cb72190_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 4
        echo "<h1>Games Sessions</h1>

</br>

<form action=\"";
        // line 8
        echo $this->env->getExtension('routing')->getPath("game_session_homepage");
        echo "\">
\t<input type=\"submit\" value=\"Return\">
</form>

</br>

<table>
\t<tr>
\t\t<td>Name Game</td>
\t\t<td>Rol Game</td>
\t\t<td>Master</td>
\t\t<td>Language</td>
\t\t<td>Standard View</td>
\t\t<td>Access</td>
\t</tr>
";
        // line 23
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["game_sessions_with_owner"]) ? $context["game_sessions_with_owner"] : $this->getContext($context, "game_sessions_with_owner")));
        foreach ($context['_seq'] as $context["_key"] => $context["game_session_with_owner"]) {
            // line 24
            echo "    <tr>
\t\t<td>";
            // line 25
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["game_session_with_owner"], "array_gameSession", array(), "array"), "getName", array(), "method"), "html", null, true);
            echo "</td>
\t\t<td>";
            // line 26
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["game_session_with_owner"], "array_gameSession", array(), "array"), "getRolGame", array(), "method"), "html", null, true);
            echo "</td>
\t\t<td>";
            // line 27
            echo twig_escape_filter($this->env, $this->getAttribute($context["game_session_with_owner"], "username", array(), "array"), "html", null, true);
            echo "</td>
\t\t<td>";
            // line 28
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["game_session_with_owner"], "array_gameSession", array(), "array"), "getLanguage", array(), "method"), "html", null, true);
            echo "</td>
\t\t<td>";
            // line 29
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["game_session_with_owner"], "array_gameSession", array(), "array"), "getStandardView", array(), "method"), "html", null, true);
            echo "</td>
\t\t<td>
\t\t\t<form action=\"";
            // line 31
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("join_session", array("session_id" => $this->getAttribute($this->getAttribute($context["game_session_with_owner"], "array_gameSession", array(), "array"), "getId", array(), "method"))), "html", null, true);
            echo "\">
\t    \t\t<input type=\"submit\" value=\"Join\">
\t\t\t</form>\t
\t\t</td>
    </tr>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['game_session_with_owner'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        echo "</table>


";
        // line 53
        echo "

";
        
        $__internal_0d2c8af5d51e075b1e6dd5a9d22d0297db66ebe288955534193519565cb72190->leave($__internal_0d2c8af5d51e075b1e6dd5a9d22d0297db66ebe288955534193519565cb72190_prof);

    }

    public function getTemplateName()
    {
        return "GameSessionBundle:GameSession:gameSessionsList.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 53,  104 => 37,  92 => 31,  87 => 29,  83 => 28,  79 => 27,  75 => 26,  71 => 25,  68 => 24,  64 => 23,  46 => 8,  40 => 4,  34 => 3,  11 => 1,);
    }
}

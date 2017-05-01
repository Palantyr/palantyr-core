<?php

/* @WebProfiler/Collector/exception.html.twig */
class __TwigTemplate_48bea09ebd00fb56f2a8703429db06968d6a07e8f872d1042d9213fcdeca6e03 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/exception.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'menu' => array($this, 'block_menu'),
            'panel' => array($this, 'block_panel'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_6ff55cbc5dd5940b96ae941bb32e21a36ca9f733d90ff9e981c87090f9eabd4f = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_6ff55cbc5dd5940b96ae941bb32e21a36ca9f733d90ff9e981c87090f9eabd4f->enter($__internal_6ff55cbc5dd5940b96ae941bb32e21a36ca9f733d90ff9e981c87090f9eabd4f_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/exception.html.twig"));

        $__internal_0266672405e20d7e3ee98e0af1233d6c9f468ea78dcf884c4221b37e25c3ad41 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_0266672405e20d7e3ee98e0af1233d6c9f468ea78dcf884c4221b37e25c3ad41->enter($__internal_0266672405e20d7e3ee98e0af1233d6c9f468ea78dcf884c4221b37e25c3ad41_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/exception.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_6ff55cbc5dd5940b96ae941bb32e21a36ca9f733d90ff9e981c87090f9eabd4f->leave($__internal_6ff55cbc5dd5940b96ae941bb32e21a36ca9f733d90ff9e981c87090f9eabd4f_prof);

        
        $__internal_0266672405e20d7e3ee98e0af1233d6c9f468ea78dcf884c4221b37e25c3ad41->leave($__internal_0266672405e20d7e3ee98e0af1233d6c9f468ea78dcf884c4221b37e25c3ad41_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_3d7a87b6b6a02b7196a84447af074fcc5b618fe8fb915942a52d27cc51bc6d19 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_3d7a87b6b6a02b7196a84447af074fcc5b618fe8fb915942a52d27cc51bc6d19->enter($__internal_3d7a87b6b6a02b7196a84447af074fcc5b618fe8fb915942a52d27cc51bc6d19_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        $__internal_90fa4a8c117fe3f01c04c02d80a72a12ac1837660c60555f3d050b1d3629cf4a = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_90fa4a8c117fe3f01c04c02d80a72a12ac1837660c60555f3d050b1d3629cf4a->enter($__internal_90fa4a8c117fe3f01c04c02d80a72a12ac1837660c60555f3d050b1d3629cf4a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    ";
        if (twig_get_attribute($this->env, $this->getSourceContext(), (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new Twig_Error_Runtime('Variable "collector" does not exist.', 4, $this->getSourceContext()); })()), "hasexception", array())) {
            // line 5
            echo "        <style>
            ";
            // line 6
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment($this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("_profiler_exception_css", array("token" => (isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new Twig_Error_Runtime('Variable "token" does not exist.', 6, $this->getSourceContext()); })()))));
            echo "
        </style>
    ";
        }
        // line 9
        echo "    ";
        $this->displayParentBlock("head", $context, $blocks);
        echo "
";
        
        $__internal_90fa4a8c117fe3f01c04c02d80a72a12ac1837660c60555f3d050b1d3629cf4a->leave($__internal_90fa4a8c117fe3f01c04c02d80a72a12ac1837660c60555f3d050b1d3629cf4a_prof);

        
        $__internal_3d7a87b6b6a02b7196a84447af074fcc5b618fe8fb915942a52d27cc51bc6d19->leave($__internal_3d7a87b6b6a02b7196a84447af074fcc5b618fe8fb915942a52d27cc51bc6d19_prof);

    }

    // line 12
    public function block_menu($context, array $blocks = array())
    {
        $__internal_6fba86ff6fe34c727e281c635b58302b4256610008f2542fa89f903760e1e69c = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_6fba86ff6fe34c727e281c635b58302b4256610008f2542fa89f903760e1e69c->enter($__internal_6fba86ff6fe34c727e281c635b58302b4256610008f2542fa89f903760e1e69c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        $__internal_3a7029148ba5133a542a9de8f70057dee7cb761d4a522872dbc9c9b260306a52 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_3a7029148ba5133a542a9de8f70057dee7cb761d4a522872dbc9c9b260306a52->enter($__internal_3a7029148ba5133a542a9de8f70057dee7cb761d4a522872dbc9c9b260306a52_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 13
        echo "    <span class=\"label ";
        echo ((twig_get_attribute($this->env, $this->getSourceContext(), (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new Twig_Error_Runtime('Variable "collector" does not exist.', 13, $this->getSourceContext()); })()), "hasexception", array())) ? ("label-status-error") : ("disabled"));
        echo "\">
        <span class=\"icon\">";
        // line 14
        echo twig_include($this->env, $context, "@WebProfiler/Icon/exception.svg");
        echo "</span>
        <strong>Exception</strong>
        ";
        // line 16
        if (twig_get_attribute($this->env, $this->getSourceContext(), (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new Twig_Error_Runtime('Variable "collector" does not exist.', 16, $this->getSourceContext()); })()), "hasexception", array())) {
            // line 17
            echo "            <span class=\"count\">
                <span>1</span>
            </span>
        ";
        }
        // line 21
        echo "    </span>
";
        
        $__internal_3a7029148ba5133a542a9de8f70057dee7cb761d4a522872dbc9c9b260306a52->leave($__internal_3a7029148ba5133a542a9de8f70057dee7cb761d4a522872dbc9c9b260306a52_prof);

        
        $__internal_6fba86ff6fe34c727e281c635b58302b4256610008f2542fa89f903760e1e69c->leave($__internal_6fba86ff6fe34c727e281c635b58302b4256610008f2542fa89f903760e1e69c_prof);

    }

    // line 24
    public function block_panel($context, array $blocks = array())
    {
        $__internal_fdf88a31348645940b2b8a8c0e2d1ec9c40ecf27d0ca7baaac6252a7829f7b61 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_fdf88a31348645940b2b8a8c0e2d1ec9c40ecf27d0ca7baaac6252a7829f7b61->enter($__internal_fdf88a31348645940b2b8a8c0e2d1ec9c40ecf27d0ca7baaac6252a7829f7b61_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        $__internal_1cd97ba7d52e12585e6529c0590204aa1aab14705e3b2992914ad1ef2c9b545a = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_1cd97ba7d52e12585e6529c0590204aa1aab14705e3b2992914ad1ef2c9b545a->enter($__internal_1cd97ba7d52e12585e6529c0590204aa1aab14705e3b2992914ad1ef2c9b545a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 25
        echo "    <h2>Exceptions</h2>

    ";
        // line 27
        if ( !twig_get_attribute($this->env, $this->getSourceContext(), (isset($context["collector"]) || array_key_exists("collector", $context) ? $context["collector"] : (function () { throw new Twig_Error_Runtime('Variable "collector" does not exist.', 27, $this->getSourceContext()); })()), "hasexception", array())) {
            // line 28
            echo "        <div class=\"empty\">
            <p>No exception was thrown and caught during the request.</p>
        </div>
    ";
        } else {
            // line 32
            echo "        <div class=\"sf-reset\">
            ";
            // line 33
            echo $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment($this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("_profiler_exception", array("token" => (isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new Twig_Error_Runtime('Variable "token" does not exist.', 33, $this->getSourceContext()); })()))));
            echo "
        </div>
    ";
        }
        
        $__internal_1cd97ba7d52e12585e6529c0590204aa1aab14705e3b2992914ad1ef2c9b545a->leave($__internal_1cd97ba7d52e12585e6529c0590204aa1aab14705e3b2992914ad1ef2c9b545a_prof);

        
        $__internal_fdf88a31348645940b2b8a8c0e2d1ec9c40ecf27d0ca7baaac6252a7829f7b61->leave($__internal_fdf88a31348645940b2b8a8c0e2d1ec9c40ecf27d0ca7baaac6252a7829f7b61_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/exception.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  138 => 33,  135 => 32,  129 => 28,  127 => 27,  123 => 25,  114 => 24,  103 => 21,  97 => 17,  95 => 16,  90 => 14,  85 => 13,  76 => 12,  63 => 9,  57 => 6,  54 => 5,  51 => 4,  42 => 3,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block head %}
    {% if collector.hasexception %}
        <style>
            {{ render(path('_profiler_exception_css', { token: token })) }}
        </style>
    {% endif %}
    {{ parent() }}
{% endblock %}

{% block menu %}
    <span class=\"label {{ collector.hasexception ? 'label-status-error' : 'disabled' }}\">
        <span class=\"icon\">{{ include('@WebProfiler/Icon/exception.svg') }}</span>
        <strong>Exception</strong>
        {% if collector.hasexception %}
            <span class=\"count\">
                <span>1</span>
            </span>
        {% endif %}
    </span>
{% endblock %}

{% block panel %}
    <h2>Exceptions</h2>

    {% if not collector.hasexception %}
        <div class=\"empty\">
            <p>No exception was thrown and caught during the request.</p>
        </div>
    {% else %}
        <div class=\"sf-reset\">
            {{ render(path('_profiler_exception', { token: token })) }}
        </div>
    {% endif %}
{% endblock %}
", "@WebProfiler/Collector/exception.html.twig", "/media/sf_SHARE/PalantirNew/vendor/symfony/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/views/Collector/exception.html.twig");
    }
}

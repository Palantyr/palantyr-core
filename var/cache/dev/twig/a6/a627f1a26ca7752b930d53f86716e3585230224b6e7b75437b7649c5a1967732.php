<?php

/* @WebProfiler/Collector/ajax.html.twig */
class __TwigTemplate_491d5853a96629e58d52ecc633bb16e5a88480f3bee41d89b781a0d49240981a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/ajax.html.twig", 1);
        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@WebProfiler/Profiler/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_f2133f576115d5138a2af4162cd7732dadf03c5f7d42786e3995c367d62d091c = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_f2133f576115d5138a2af4162cd7732dadf03c5f7d42786e3995c367d62d091c->enter($__internal_f2133f576115d5138a2af4162cd7732dadf03c5f7d42786e3995c367d62d091c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/ajax.html.twig"));

        $__internal_69487f82ffc450c7e3a60265e41f7f9e45e82daf297d570327d3b6e0a08789f9 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_69487f82ffc450c7e3a60265e41f7f9e45e82daf297d570327d3b6e0a08789f9->enter($__internal_69487f82ffc450c7e3a60265e41f7f9e45e82daf297d570327d3b6e0a08789f9_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/ajax.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_f2133f576115d5138a2af4162cd7732dadf03c5f7d42786e3995c367d62d091c->leave($__internal_f2133f576115d5138a2af4162cd7732dadf03c5f7d42786e3995c367d62d091c_prof);

        
        $__internal_69487f82ffc450c7e3a60265e41f7f9e45e82daf297d570327d3b6e0a08789f9->leave($__internal_69487f82ffc450c7e3a60265e41f7f9e45e82daf297d570327d3b6e0a08789f9_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_d94ef9b9dd2ad243132e2fb41b5136ca3b6d9dbe0b7bd6f36a165a0e6a30f608 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_d94ef9b9dd2ad243132e2fb41b5136ca3b6d9dbe0b7bd6f36a165a0e6a30f608->enter($__internal_d94ef9b9dd2ad243132e2fb41b5136ca3b6d9dbe0b7bd6f36a165a0e6a30f608_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        $__internal_9de5a21b0682a4a7c950e513f996be887ac2b70755e788c2803916083c915044 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_9de5a21b0682a4a7c950e513f996be887ac2b70755e788c2803916083c915044->enter($__internal_9de5a21b0682a4a7c950e513f996be887ac2b70755e788c2803916083c915044_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        // line 4
        echo "    ";
        ob_start();
        // line 5
        echo "        ";
        echo twig_include($this->env, $context, "@WebProfiler/Icon/ajax.svg");
        echo "
        <span class=\"sf-toolbar-value sf-toolbar-ajax-requests\">0</span>
    ";
        $context["icon"] = ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 8
        echo "
    ";
        // line 9
        $context["text"] = ('' === $tmp = "        <div class=\"sf-toolbar-info-piece\">
            <b class=\"sf-toolbar-ajax-info\"></b>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <table class=\"sf-toolbar-ajax-requests\">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>URL</th>
                        <th>Time</th>
                        <th>Profile</th>
                    </tr>
                </thead>
                <tbody class=\"sf-toolbar-ajax-request-list\"></tbody>
            </table>
        </div>
    ") ? '' : new Twig_Markup($tmp, $this->env->getCharset());
        // line 29
        echo "
    ";
        // line 30
        echo twig_include($this->env, $context, "@WebProfiler/Profiler/toolbar_item.html.twig", array("link" => false));
        echo "
";
        
        $__internal_9de5a21b0682a4a7c950e513f996be887ac2b70755e788c2803916083c915044->leave($__internal_9de5a21b0682a4a7c950e513f996be887ac2b70755e788c2803916083c915044_prof);

        
        $__internal_d94ef9b9dd2ad243132e2fb41b5136ca3b6d9dbe0b7bd6f36a165a0e6a30f608->leave($__internal_d94ef9b9dd2ad243132e2fb41b5136ca3b6d9dbe0b7bd6f36a165a0e6a30f608_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/ajax.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 30,  82 => 29,  62 => 9,  59 => 8,  52 => 5,  49 => 4,  40 => 3,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block toolbar %}
    {% set icon %}
        {{ include('@WebProfiler/Icon/ajax.svg') }}
        <span class=\"sf-toolbar-value sf-toolbar-ajax-requests\">0</span>
    {% endset %}

    {% set text %}
        <div class=\"sf-toolbar-info-piece\">
            <b class=\"sf-toolbar-ajax-info\"></b>
        </div>
        <div class=\"sf-toolbar-info-piece\">
            <table class=\"sf-toolbar-ajax-requests\">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>URL</th>
                        <th>Time</th>
                        <th>Profile</th>
                    </tr>
                </thead>
                <tbody class=\"sf-toolbar-ajax-request-list\"></tbody>
            </table>
        </div>
    {% endset %}

    {{ include('@WebProfiler/Profiler/toolbar_item.html.twig', { link: false }) }}
{% endblock %}
", "@WebProfiler/Collector/ajax.html.twig", "/media/sf_SHARE/PalantirNew/vendor/symfony/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/views/Collector/ajax.html.twig");
    }
}

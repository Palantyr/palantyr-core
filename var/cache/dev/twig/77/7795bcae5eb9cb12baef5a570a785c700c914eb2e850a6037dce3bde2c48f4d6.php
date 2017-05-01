<?php

/* @WebProfiler/Collector/router.html.twig */
class __TwigTemplate_ea1eba626b721d5f2f2b54b1e4a553c84fdc8f20c6305b61e40a9951a6d8df25 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("@WebProfiler/Profiler/layout.html.twig", "@WebProfiler/Collector/router.html.twig", 1);
        $this->blocks = array(
            'toolbar' => array($this, 'block_toolbar'),
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
        $__internal_2a0320427ad471f9f07f98fe9c2c51b11d6723ebc11f8be7f6c9178d8ce2971a = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_2a0320427ad471f9f07f98fe9c2c51b11d6723ebc11f8be7f6c9178d8ce2971a->enter($__internal_2a0320427ad471f9f07f98fe9c2c51b11d6723ebc11f8be7f6c9178d8ce2971a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/router.html.twig"));

        $__internal_d86ee1c4021d7c4ab165f83abc61c3d582ba4e5ea521b9674574a66dd93f0e92 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_d86ee1c4021d7c4ab165f83abc61c3d582ba4e5ea521b9674574a66dd93f0e92->enter($__internal_d86ee1c4021d7c4ab165f83abc61c3d582ba4e5ea521b9674574a66dd93f0e92_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "@WebProfiler/Collector/router.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_2a0320427ad471f9f07f98fe9c2c51b11d6723ebc11f8be7f6c9178d8ce2971a->leave($__internal_2a0320427ad471f9f07f98fe9c2c51b11d6723ebc11f8be7f6c9178d8ce2971a_prof);

        
        $__internal_d86ee1c4021d7c4ab165f83abc61c3d582ba4e5ea521b9674574a66dd93f0e92->leave($__internal_d86ee1c4021d7c4ab165f83abc61c3d582ba4e5ea521b9674574a66dd93f0e92_prof);

    }

    // line 3
    public function block_toolbar($context, array $blocks = array())
    {
        $__internal_5c364098effb236a6a8b39f02a8be396abdd3bb52ef652192bc8327a26a23df0 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_5c364098effb236a6a8b39f02a8be396abdd3bb52ef652192bc8327a26a23df0->enter($__internal_5c364098effb236a6a8b39f02a8be396abdd3bb52ef652192bc8327a26a23df0_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        $__internal_a89c27d5680df70ea27fdb722376a61ef903d982be2ca058cfe256bf08ad498c = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_a89c27d5680df70ea27fdb722376a61ef903d982be2ca058cfe256bf08ad498c->enter($__internal_a89c27d5680df70ea27fdb722376a61ef903d982be2ca058cfe256bf08ad498c_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "toolbar"));

        
        $__internal_a89c27d5680df70ea27fdb722376a61ef903d982be2ca058cfe256bf08ad498c->leave($__internal_a89c27d5680df70ea27fdb722376a61ef903d982be2ca058cfe256bf08ad498c_prof);

        
        $__internal_5c364098effb236a6a8b39f02a8be396abdd3bb52ef652192bc8327a26a23df0->leave($__internal_5c364098effb236a6a8b39f02a8be396abdd3bb52ef652192bc8327a26a23df0_prof);

    }

    // line 5
    public function block_menu($context, array $blocks = array())
    {
        $__internal_a45a617c731e65b3271cf3d2b8917ecb2b23dc3228c45837a003c5b12dd41865 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_a45a617c731e65b3271cf3d2b8917ecb2b23dc3228c45837a003c5b12dd41865->enter($__internal_a45a617c731e65b3271cf3d2b8917ecb2b23dc3228c45837a003c5b12dd41865_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        $__internal_5b0a78f7bc9adf335f7b8431372058e99fe7bb889ccfa0b04317a510a36f4be5 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_5b0a78f7bc9adf335f7b8431372058e99fe7bb889ccfa0b04317a510a36f4be5->enter($__internal_5b0a78f7bc9adf335f7b8431372058e99fe7bb889ccfa0b04317a510a36f4be5_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "menu"));

        // line 6
        echo "<span class=\"label\">
    <span class=\"icon\">";
        // line 7
        echo twig_include($this->env, $context, "@WebProfiler/Icon/router.svg");
        echo "</span>
    <strong>Routing</strong>
</span>
";
        
        $__internal_5b0a78f7bc9adf335f7b8431372058e99fe7bb889ccfa0b04317a510a36f4be5->leave($__internal_5b0a78f7bc9adf335f7b8431372058e99fe7bb889ccfa0b04317a510a36f4be5_prof);

        
        $__internal_a45a617c731e65b3271cf3d2b8917ecb2b23dc3228c45837a003c5b12dd41865->leave($__internal_a45a617c731e65b3271cf3d2b8917ecb2b23dc3228c45837a003c5b12dd41865_prof);

    }

    // line 12
    public function block_panel($context, array $blocks = array())
    {
        $__internal_3e25656901e73149e722a9ed59885be84e1a57b43e514a19c7e4ccb3e56d3792 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_3e25656901e73149e722a9ed59885be84e1a57b43e514a19c7e4ccb3e56d3792->enter($__internal_3e25656901e73149e722a9ed59885be84e1a57b43e514a19c7e4ccb3e56d3792_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        $__internal_c1fe436703264f4a709541bc309f69b328efc813ef371bf4bec0716c8f479920 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_c1fe436703264f4a709541bc309f69b328efc813ef371bf4bec0716c8f479920->enter($__internal_c1fe436703264f4a709541bc309f69b328efc813ef371bf4bec0716c8f479920_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "panel"));

        // line 13
        echo "    ";
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment($this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("_profiler_router", array("token" => (isset($context["token"]) || array_key_exists("token", $context) ? $context["token"] : (function () { throw new Twig_Error_Runtime('Variable "token" does not exist.', 13, $this->getSourceContext()); })()))));
        echo "
";
        
        $__internal_c1fe436703264f4a709541bc309f69b328efc813ef371bf4bec0716c8f479920->leave($__internal_c1fe436703264f4a709541bc309f69b328efc813ef371bf4bec0716c8f479920_prof);

        
        $__internal_3e25656901e73149e722a9ed59885be84e1a57b43e514a19c7e4ccb3e56d3792->leave($__internal_3e25656901e73149e722a9ed59885be84e1a57b43e514a19c7e4ccb3e56d3792_prof);

    }

    public function getTemplateName()
    {
        return "@WebProfiler/Collector/router.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 13,  85 => 12,  71 => 7,  68 => 6,  59 => 5,  42 => 3,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block toolbar %}{% endblock %}

{% block menu %}
<span class=\"label\">
    <span class=\"icon\">{{ include('@WebProfiler/Icon/router.svg') }}</span>
    <strong>Routing</strong>
</span>
{% endblock %}

{% block panel %}
    {{ render(path('_profiler_router', { token: token })) }}
{% endblock %}
", "@WebProfiler/Collector/router.html.twig", "/media/sf_SHARE/PalantirNew/vendor/symfony/symfony/src/Symfony/Bundle/WebProfilerBundle/Resources/views/Collector/router.html.twig");
    }
}

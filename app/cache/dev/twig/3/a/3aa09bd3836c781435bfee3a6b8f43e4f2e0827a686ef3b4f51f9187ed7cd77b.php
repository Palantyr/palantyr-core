<?php

/* UserBundle:Account:register.html.twig */
class __TwigTemplate_3aa09bd3836c781435bfee3a6b8f43e4f2e0827a686ef3b4f51f9187ed7cd77b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 2
        $this->parent = $this->loadTemplate("base.html.twig", "UserBundle:Account:register.html.twig", 2);
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
        $__internal_f459be5b3a9386f5b07f34f4bcf85f9a951fa7b74bac407f0991e29ee02bb8ef = $this->env->getExtension("native_profiler");
        $__internal_f459be5b3a9386f5b07f34f4bcf85f9a951fa7b74bac407f0991e29ee02bb8ef->enter($__internal_f459be5b3a9386f5b07f34f4bcf85f9a951fa7b74bac407f0991e29ee02bb8ef_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "UserBundle:Account:register.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_f459be5b3a9386f5b07f34f4bcf85f9a951fa7b74bac407f0991e29ee02bb8ef->leave($__internal_f459be5b3a9386f5b07f34f4bcf85f9a951fa7b74bac407f0991e29ee02bb8ef_prof);

    }

    // line 4
    public function block_body($context, array $blocks = array())
    {
        $__internal_d1d8d85fb26bba6e70f880bf510bb8991735ada807d9d5becc09065db9611763 = $this->env->getExtension("native_profiler");
        $__internal_d1d8d85fb26bba6e70f880bf510bb8991735ada807d9d5becc09065db9611763->enter($__internal_d1d8d85fb26bba6e70f880bf510bb8991735ada807d9d5becc09065db9611763_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 5
        echo         $this->env->getExtension('form')->renderer->renderBlock((isset($context["form"]) ? $context["form"] : $this->getContext($context, "form")), 'form');
        echo "
</br>
<form action=\"";
        // line 7
        echo $this->env->getExtension('routing')->getPath("app_index");
        echo "\">
\t<input type=\"submit\" value=\"Return\">
</form>
";
        
        $__internal_d1d8d85fb26bba6e70f880bf510bb8991735ada807d9d5becc09065db9611763->leave($__internal_d1d8d85fb26bba6e70f880bf510bb8991735ada807d9d5becc09065db9611763_prof);

    }

    public function getTemplateName()
    {
        return "UserBundle:Account:register.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 7,  40 => 5,  34 => 4,  11 => 2,);
    }
}

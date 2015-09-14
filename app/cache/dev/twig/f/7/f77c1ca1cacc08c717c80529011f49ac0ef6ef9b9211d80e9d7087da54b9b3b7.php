<?php

/* base.html.twig */
class __TwigTemplate_f77c1ca1cacc08c717c80529011f49ac0ef6ef9b9211d80e9d7087da54b9b3b7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_ed393365ffee82cd5e63d50b5526970e7354568ec8329b9fd34257f1a944f989 = $this->env->getExtension("native_profiler");
        $__internal_ed393365ffee82cd5e63d50b5526970e7354568ec8329b9fd34257f1a944f989->enter($__internal_ed393365ffee82cd5e63d50b5526970e7354568ec8329b9fd34257f1a944f989_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "base.html.twig"));

        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
        ";
        // line 6
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 7
        echo "        <link rel=\"icon\" type=\"image/x-icon\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('asset')->getAssetUrl("favicon.ico"), "html", null, true);
        echo "\" />
    </head>
    <body>
        ";
        // line 10
        $this->displayBlock('body', $context, $blocks);
        // line 11
        echo "        ";
        $this->displayBlock('javascripts', $context, $blocks);
        // line 12
        echo "    </body>
</html>
";
        
        $__internal_ed393365ffee82cd5e63d50b5526970e7354568ec8329b9fd34257f1a944f989->leave($__internal_ed393365ffee82cd5e63d50b5526970e7354568ec8329b9fd34257f1a944f989_prof);

    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        $__internal_bcfded0da3ba51ad3117fb791519e2dac95cab1da4c210627e5aea89a56d0f0d = $this->env->getExtension("native_profiler");
        $__internal_bcfded0da3ba51ad3117fb791519e2dac95cab1da4c210627e5aea89a56d0f0d->enter($__internal_bcfded0da3ba51ad3117fb791519e2dac95cab1da4c210627e5aea89a56d0f0d_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        echo "Welcome!";
        
        $__internal_bcfded0da3ba51ad3117fb791519e2dac95cab1da4c210627e5aea89a56d0f0d->leave($__internal_bcfded0da3ba51ad3117fb791519e2dac95cab1da4c210627e5aea89a56d0f0d_prof);

    }

    // line 6
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_b12fc52a8158559c4a33595f8a65616387bf2719f1d34cc88c2e2b0b9bc19aff = $this->env->getExtension("native_profiler");
        $__internal_b12fc52a8158559c4a33595f8a65616387bf2719f1d34cc88c2e2b0b9bc19aff->enter($__internal_b12fc52a8158559c4a33595f8a65616387bf2719f1d34cc88c2e2b0b9bc19aff_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_b12fc52a8158559c4a33595f8a65616387bf2719f1d34cc88c2e2b0b9bc19aff->leave($__internal_b12fc52a8158559c4a33595f8a65616387bf2719f1d34cc88c2e2b0b9bc19aff_prof);

    }

    // line 10
    public function block_body($context, array $blocks = array())
    {
        $__internal_d27d1128db30b000a4cad672c350a8e501def68f69a2ff42c7446c57569d44c9 = $this->env->getExtension("native_profiler");
        $__internal_d27d1128db30b000a4cad672c350a8e501def68f69a2ff42c7446c57569d44c9->enter($__internal_d27d1128db30b000a4cad672c350a8e501def68f69a2ff42c7446c57569d44c9_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        
        $__internal_d27d1128db30b000a4cad672c350a8e501def68f69a2ff42c7446c57569d44c9->leave($__internal_d27d1128db30b000a4cad672c350a8e501def68f69a2ff42c7446c57569d44c9_prof);

    }

    // line 11
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_437fdc0e3b3ec93c13c1e8cc8cae0f4ff3fa43f5f545b61376d92376cf2d4451 = $this->env->getExtension("native_profiler");
        $__internal_437fdc0e3b3ec93c13c1e8cc8cae0f4ff3fa43f5f545b61376d92376cf2d4451->enter($__internal_437fdc0e3b3ec93c13c1e8cc8cae0f4ff3fa43f5f545b61376d92376cf2d4451_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_437fdc0e3b3ec93c13c1e8cc8cae0f4ff3fa43f5f545b61376d92376cf2d4451->leave($__internal_437fdc0e3b3ec93c13c1e8cc8cae0f4ff3fa43f5f545b61376d92376cf2d4451_prof);

    }

    public function getTemplateName()
    {
        return "base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  93 => 11,  82 => 10,  71 => 6,  59 => 5,  50 => 12,  47 => 11,  45 => 10,  38 => 7,  36 => 6,  32 => 5,  26 => 1,);
    }
}

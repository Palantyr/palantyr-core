<?php

/* TwigBundle:Exception:exception_full.html.twig */
class __TwigTemplate_e3c498148c88090c9b8d4ce529173e84cf98396a060bf51cc2f26598cca72b67 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("TwigBundle::layout.html.twig", "TwigBundle:Exception:exception_full.html.twig", 1);
        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "TwigBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_91caa761547a213c9c7803a6eb2f41ab02a4257ec7ebd69c5a642b7e0e4ab25a = $this->env->getExtension("native_profiler");
        $__internal_91caa761547a213c9c7803a6eb2f41ab02a4257ec7ebd69c5a642b7e0e4ab25a->enter($__internal_91caa761547a213c9c7803a6eb2f41ab02a4257ec7ebd69c5a642b7e0e4ab25a_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "TwigBundle:Exception:exception_full.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_91caa761547a213c9c7803a6eb2f41ab02a4257ec7ebd69c5a642b7e0e4ab25a->leave($__internal_91caa761547a213c9c7803a6eb2f41ab02a4257ec7ebd69c5a642b7e0e4ab25a_prof);

    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        $__internal_9ff94bad67a6134dba7334223de3cb2315a068acbe1908b024df05198ba81a26 = $this->env->getExtension("native_profiler");
        $__internal_9ff94bad67a6134dba7334223de3cb2315a068acbe1908b024df05198ba81a26->enter($__internal_9ff94bad67a6134dba7334223de3cb2315a068acbe1908b024df05198ba81a26_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "head"));

        // line 4
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('request')->generateAbsoluteUrl($this->env->getExtension('asset')->getAssetUrl("bundles/framework/css/exception.css")), "html", null, true);
        echo "\" rel=\"stylesheet\" type=\"text/css\" media=\"all\" />
";
        
        $__internal_9ff94bad67a6134dba7334223de3cb2315a068acbe1908b024df05198ba81a26->leave($__internal_9ff94bad67a6134dba7334223de3cb2315a068acbe1908b024df05198ba81a26_prof);

    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        $__internal_201c197d35a9103f9a04a089b87cb20bf4eff2a6f4840f8ee5d7a84df2eaac81 = $this->env->getExtension("native_profiler");
        $__internal_201c197d35a9103f9a04a089b87cb20bf4eff2a6f4840f8ee5d7a84df2eaac81->enter($__internal_201c197d35a9103f9a04a089b87cb20bf4eff2a6f4840f8ee5d7a84df2eaac81_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        // line 8
        echo "    ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["exception"]) ? $context["exception"] : $this->getContext($context, "exception")), "message", array()), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, (isset($context["status_code"]) ? $context["status_code"] : $this->getContext($context, "status_code")), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["status_text"]) ? $context["status_text"] : $this->getContext($context, "status_text")), "html", null, true);
        echo ")
";
        
        $__internal_201c197d35a9103f9a04a089b87cb20bf4eff2a6f4840f8ee5d7a84df2eaac81->leave($__internal_201c197d35a9103f9a04a089b87cb20bf4eff2a6f4840f8ee5d7a84df2eaac81_prof);

    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        $__internal_a3819835d9d5d4d3614cac330830a718f50d5326876c4e1044f43d6483340795 = $this->env->getExtension("native_profiler");
        $__internal_a3819835d9d5d4d3614cac330830a718f50d5326876c4e1044f43d6483340795->enter($__internal_a3819835d9d5d4d3614cac330830a718f50d5326876c4e1044f43d6483340795_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 12
        echo "    ";
        $this->loadTemplate("TwigBundle:Exception:exception.html.twig", "TwigBundle:Exception:exception_full.html.twig", 12)->display($context);
        
        $__internal_a3819835d9d5d4d3614cac330830a718f50d5326876c4e1044f43d6483340795->leave($__internal_a3819835d9d5d4d3614cac330830a718f50d5326876c4e1044f43d6483340795_prof);

    }

    public function getTemplateName()
    {
        return "TwigBundle:Exception:exception_full.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  78 => 12,  72 => 11,  58 => 8,  52 => 7,  42 => 4,  36 => 3,  11 => 1,);
    }
}

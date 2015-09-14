<?php

/* UserBundle:Security:login.html.twig */
class __TwigTemplate_436c9b553b9ca59e59e6a9ae12edad39823f52de197ea782f5e16506d0f73e44 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 2
        $this->parent = $this->loadTemplate("base.html.twig", "UserBundle:Security:login.html.twig", 2);
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
        $__internal_cd550a1a8e492004bf82d271127ce3389b71416a452f245f37b1ea4731b1ff83 = $this->env->getExtension("native_profiler");
        $__internal_cd550a1a8e492004bf82d271127ce3389b71416a452f245f37b1ea4731b1ff83->enter($__internal_cd550a1a8e492004bf82d271127ce3389b71416a452f245f37b1ea4731b1ff83_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "UserBundle:Security:login.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_cd550a1a8e492004bf82d271127ce3389b71416a452f245f37b1ea4731b1ff83->leave($__internal_cd550a1a8e492004bf82d271127ce3389b71416a452f245f37b1ea4731b1ff83_prof);

    }

    // line 4
    public function block_body($context, array $blocks = array())
    {
        $__internal_9f8752a7b03b99bbe1ef04a214128341f288b5197bba10208f1ac6aa6ed68db3 = $this->env->getExtension("native_profiler");
        $__internal_9f8752a7b03b99bbe1ef04a214128341f288b5197bba10208f1ac6aa6ed68db3->enter($__internal_9f8752a7b03b99bbe1ef04a214128341f288b5197bba10208f1ac6aa6ed68db3_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 5
        if ((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error"))) {
            // line 6
            echo "    <div>";
            echo twig_escape_filter($this->env, $this->env->getExtension('translator')->trans($this->getAttribute((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "messageKey", array()), $this->getAttribute((isset($context["error"]) ? $context["error"] : $this->getContext($context, "error")), "messageData", array()), "security"), "html", null, true);
            echo "</div>
";
        }
        // line 8
        echo " 
<form action=\"";
        // line 9
        echo $this->env->getExtension('routing')->getPath("login_check");
        echo "\" method=\"post\">
    <label for=\"username\">Username:</label>
    <input type=\"text\" id=\"username\" name=\"_username\" value=\"";
        // line 11
        echo twig_escape_filter($this->env, (isset($context["last_username"]) ? $context["last_username"] : $this->getContext($context, "last_username")), "html", null, true);
        echo "\" />
 
    <label for=\"password\">Password:</label>
    <input type=\"password\" id=\"password\" name=\"_password\" />
 
    ";
        // line 21
        echo "    
    <button type=\"submit\">login</button>
</form>

</br>

<form action=\"";
        // line 27
        echo $this->env->getExtension('routing')->getPath("app_index");
        echo "\">
\t<input type=\"submit\" value=\"Return\">
</form>

";
        
        $__internal_9f8752a7b03b99bbe1ef04a214128341f288b5197bba10208f1ac6aa6ed68db3->leave($__internal_9f8752a7b03b99bbe1ef04a214128341f288b5197bba10208f1ac6aa6ed68db3_prof);

    }

    public function getTemplateName()
    {
        return "UserBundle:Security:login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 27,  64 => 21,  56 => 11,  51 => 9,  48 => 8,  42 => 6,  40 => 5,  34 => 4,  11 => 2,);
    }
}

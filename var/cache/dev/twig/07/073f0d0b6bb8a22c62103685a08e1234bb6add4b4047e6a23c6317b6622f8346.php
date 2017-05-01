<?php

/* base.html.twig */
class __TwigTemplate_12a9784906875215e0dff72fbea6a70a80828e4aa3365ff25286dac4ee04c2cf extends Twig_Template
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
        $__internal_3a4e6f986bfcabae70df678096efea29a7575acb262245aa9490b5642f9580f1 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_3a4e6f986bfcabae70df678096efea29a7575acb262245aa9490b5642f9580f1->enter($__internal_3a4e6f986bfcabae70df678096efea29a7575acb262245aa9490b5642f9580f1_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "base.html.twig"));

        $__internal_11186767835c41b4d213bbc5e2bdeab667f953140aa63e35de6c3fd2cf359f28 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_11186767835c41b4d213bbc5e2bdeab667f953140aa63e35de6c3fd2cf359f28->enter($__internal_11186767835c41b4d213bbc5e2bdeab667f953140aa63e35de6c3fd2cf359f28_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "base.html.twig"));

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
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("favicon.ico"), "html", null, true);
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
        
        $__internal_3a4e6f986bfcabae70df678096efea29a7575acb262245aa9490b5642f9580f1->leave($__internal_3a4e6f986bfcabae70df678096efea29a7575acb262245aa9490b5642f9580f1_prof);

        
        $__internal_11186767835c41b4d213bbc5e2bdeab667f953140aa63e35de6c3fd2cf359f28->leave($__internal_11186767835c41b4d213bbc5e2bdeab667f953140aa63e35de6c3fd2cf359f28_prof);

    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        $__internal_f75c0ef3e54d65c3a3fc8fe6d04ee590dba240b13be1c5818faf46e1b474c2f3 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_f75c0ef3e54d65c3a3fc8fe6d04ee590dba240b13be1c5818faf46e1b474c2f3->enter($__internal_f75c0ef3e54d65c3a3fc8fe6d04ee590dba240b13be1c5818faf46e1b474c2f3_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        $__internal_34b25e306b522dbeff79127ad76d3fab65fcb5b8a7b1a386406413e35df93d21 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_34b25e306b522dbeff79127ad76d3fab65fcb5b8a7b1a386406413e35df93d21->enter($__internal_34b25e306b522dbeff79127ad76d3fab65fcb5b8a7b1a386406413e35df93d21_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        echo "Welcome!";
        
        $__internal_34b25e306b522dbeff79127ad76d3fab65fcb5b8a7b1a386406413e35df93d21->leave($__internal_34b25e306b522dbeff79127ad76d3fab65fcb5b8a7b1a386406413e35df93d21_prof);

        
        $__internal_f75c0ef3e54d65c3a3fc8fe6d04ee590dba240b13be1c5818faf46e1b474c2f3->leave($__internal_f75c0ef3e54d65c3a3fc8fe6d04ee590dba240b13be1c5818faf46e1b474c2f3_prof);

    }

    // line 6
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_ff7fee20c2a83cc45bf75d6879344b319d1331889219b1226091920f1d700f26 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_ff7fee20c2a83cc45bf75d6879344b319d1331889219b1226091920f1d700f26->enter($__internal_ff7fee20c2a83cc45bf75d6879344b319d1331889219b1226091920f1d700f26_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_c54e789b747e6bc1ddddc034a2ee5002da44f4c59820a044cd9e7bd028feaf28 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_c54e789b747e6bc1ddddc034a2ee5002da44f4c59820a044cd9e7bd028feaf28->enter($__internal_c54e789b747e6bc1ddddc034a2ee5002da44f4c59820a044cd9e7bd028feaf28_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        
        $__internal_c54e789b747e6bc1ddddc034a2ee5002da44f4c59820a044cd9e7bd028feaf28->leave($__internal_c54e789b747e6bc1ddddc034a2ee5002da44f4c59820a044cd9e7bd028feaf28_prof);

        
        $__internal_ff7fee20c2a83cc45bf75d6879344b319d1331889219b1226091920f1d700f26->leave($__internal_ff7fee20c2a83cc45bf75d6879344b319d1331889219b1226091920f1d700f26_prof);

    }

    // line 10
    public function block_body($context, array $blocks = array())
    {
        $__internal_7ddba71e44a511710d57ed9d01622fd012aa72e3a91536296b4efd1ca0cd401e = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_7ddba71e44a511710d57ed9d01622fd012aa72e3a91536296b4efd1ca0cd401e->enter($__internal_7ddba71e44a511710d57ed9d01622fd012aa72e3a91536296b4efd1ca0cd401e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_5fa318edaba531401facfe4c3cd445629ec00dcbdb5311a71cc9903410d972b1 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_5fa318edaba531401facfe4c3cd445629ec00dcbdb5311a71cc9903410d972b1->enter($__internal_5fa318edaba531401facfe4c3cd445629ec00dcbdb5311a71cc9903410d972b1_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        
        $__internal_5fa318edaba531401facfe4c3cd445629ec00dcbdb5311a71cc9903410d972b1->leave($__internal_5fa318edaba531401facfe4c3cd445629ec00dcbdb5311a71cc9903410d972b1_prof);

        
        $__internal_7ddba71e44a511710d57ed9d01622fd012aa72e3a91536296b4efd1ca0cd401e->leave($__internal_7ddba71e44a511710d57ed9d01622fd012aa72e3a91536296b4efd1ca0cd401e_prof);

    }

    // line 11
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_8a8e8e6b816606d110bc17a844754f0c09a3191eed5436b89aba9a2ffa1f0da6 = $this->env->getExtension("Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension");
        $__internal_8a8e8e6b816606d110bc17a844754f0c09a3191eed5436b89aba9a2ffa1f0da6->enter($__internal_8a8e8e6b816606d110bc17a844754f0c09a3191eed5436b89aba9a2ffa1f0da6_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_13c81a32c529c1bec27035893b55d0e7b7cf4221cf9e3c830af088343e7c62b3 = $this->env->getExtension("Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension");
        $__internal_13c81a32c529c1bec27035893b55d0e7b7cf4221cf9e3c830af088343e7c62b3->enter($__internal_13c81a32c529c1bec27035893b55d0e7b7cf4221cf9e3c830af088343e7c62b3_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_13c81a32c529c1bec27035893b55d0e7b7cf4221cf9e3c830af088343e7c62b3->leave($__internal_13c81a32c529c1bec27035893b55d0e7b7cf4221cf9e3c830af088343e7c62b3_prof);

        
        $__internal_8a8e8e6b816606d110bc17a844754f0c09a3191eed5436b89aba9a2ffa1f0da6->leave($__internal_8a8e8e6b816606d110bc17a844754f0c09a3191eed5436b89aba9a2ffa1f0da6_prof);

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
        return array (  117 => 11,  100 => 10,  83 => 6,  65 => 5,  53 => 12,  50 => 11,  48 => 10,  41 => 7,  39 => 6,  35 => 5,  29 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>{% block title %}Welcome!{% endblock %}</title>
        {% block stylesheets %}{% endblock %}
        <link rel=\"icon\" type=\"image/x-icon\" href=\"{{ asset('favicon.ico') }}\" />
    </head>
    <body>
        {% block body %}{% endblock %}
        {% block javascripts %}{% endblock %}
    </body>
</html>
", "base.html.twig", "/media/sf_SHARE/PalantirNew/app/Resources/views/base.html.twig");
    }
}

<?php

/* consultant_panel.html */
class __TwigTemplate_2728474a9610d3bbeec4c9f6d5d5711472c243060e963211fd0beb747e461bee extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("_layout.html");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "_layout.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "Panel";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    <h1>Index</h1>
    <p class=\"important\">
        Welcome to my awesome homepage.
    </p>

    <form class='navbar-form navbar-right' action='index.php' method=post>
        <div class='form-group'>
            ";
        // line 13
        if ((isset($context["message"]) ? $context["message"] : null)) {
            // line 14
            echo "                <span class='alert alert-danger'>";
            echo twig_escape_filter($this->env, (isset($context["message"]) ? $context["message"] : null), "html", null, true);
            echo "</span>
            ";
        }
        // line 16
        echo "        </div>
        <div class='form-group'>
            <input type='text' placeholder='login' class='form-control' name='login'>
        </div>
        <div class='form-group'>
            <input type='password' placeholder='Password' class='form-control' name='haslo'>
        </div>
        <button type='submit' value='Submit' class='btn btn-success'>Zaloguj</button>
    </form>

";
    }

    public function getTemplateName()
    {
        return "consultant_panel.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 16,  49 => 14,  47 => 13,  38 => 6,  35 => 5,  29 => 3,);
    }
}

<?php

/* _layout.html */
class __TwigTemplate_e0b31eb2688f99ed4e12d9be099510272740a6559512a128948e8d8c66ca96c1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en\">
    <head>
        <meta charset=\"utf-8\">
        <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
        <meta name=\"description\" content=\"\">
        <meta name=\"author\" content=\"\">

        <title>";
        // line 10
        $this->displayBlock('title', $context, $blocks);
        echo " - DreamCC</title>

        <!-- Bootstrap core CSS -->
        <link href=\"assets/dist/css/bootstrap.css\" rel=\"stylesheet\">

        <!-- Custom styles for this template -->
        <link href=\"assets/jumbotron.css\" rel=\"stylesheet\">

        <!--[if lt IE 9]>
          <script src=\"https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js\"></script>
          <script src=\"https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js\"></script>
        <![endif]-->
    </head>
    <body>
        <div class=\"navbar navbar-inverse navbar-fixed-top\" role=\"navigation\">
          <div class=\"container\">
            <div class=\"navbar-header\">
              <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">
                <span class=\"sr-only\">Toggle navigation</span>
                <span class=\"icon-bar\"></span>
                <span class=\"icon-bar\"></span>
                <span class=\"icon-bar\"></span>
              </button>
                <a class=\"navbar-brand\" href=\"index.php\">Call Center</a>
            </div>
            <div class=\"navbar-collapse collapse\">
            </div><!--/.navbar-collapse -->
          </div>
        </div>

        <div class=\"jumbotron\">
          <div class=\"container\">
              ";
        // line 42
        $this->displayBlock('content', $context, $blocks);
        // line 43
        echo "        </div>
    </div>

    <div class=\"container\">
      <!-- Example row of columns -->
      <div class=\"row\">
        <div class=\"col-md-4\">

            <p></p>
        </div>
        <div class=\"col-md-4\">

       </div>
        <div class=\"col-md-4\">

        </div>
      </div>


      <hr>

      <footer>
        <p>Stowarzyszenie WIOSNA</p>
      </footer>
    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src=\"https://code.jquery.com/jquery-1.10.2.min.js\"></script>
    <script src=\"assets/dist/js/bootstrap.min.js\"></script>

        <div id=\"footer\">
            ";
        // line 76
        $this->displayBlock('footer', $context, $blocks);
        // line 79
        echo "        </div>
    </body>
</html>
";
    }

    // line 10
    public function block_title($context, array $blocks = array())
    {
    }

    // line 42
    public function block_content($context, array $blocks = array())
    {
    }

    // line 76
    public function block_footer($context, array $blocks = array())
    {
        // line 77
        echo "                &copy; Copyright 2011 by <a href=\"http://domain.invalid/\">you</a>.
            ";
    }

    public function getTemplateName()
    {
        return "_layout.html";
    }

    public function getDebugInfo()
    {
        return array (  127 => 77,  124 => 76,  119 => 42,  114 => 10,  107 => 79,  105 => 76,  70 => 43,  68 => 42,  33 => 10,  22 => 1,);
    }
}

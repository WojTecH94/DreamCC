<?php

namespace Dreamcc\Lib;

class TwigContactExtension extends \Twig_Extension
{
    function __construct($config) {

        $this->config = $config;

        return $this;
    }

    public function getName()
    {
        return 'contact';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('genLink', array($this, 'genLink')),
            new \Twig_SimpleFunction('present', array($this, 'present'))
        );
    }

    public function genLink($contact, $user) {
        $link = $this->config['lime']['address']
            . "sid/" . $contact['sid']
            . "/token/" . $contact['token']
            . "/lang//newtest/Y" . "?"
            . $contact['sid'] . "X" . $contact['operator_gid'] . "X" .$contact['operator_qid']
            . "=". $user['login'] . "&token=" . $contact['token'];

        return $link;
    }

    public function present($contact) {
        $presentation = "<p>Imię: " . $contact['firstname'] . "<br/>"
            . "Nazwisko: " . $contact['lastname'] . "<br/>"
            . "Status: " . $contact['status'] . "<br/>"
            . "Próba dotarcia: " . $contact['attempt'];
        return $presentation;
    }
}

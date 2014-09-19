<?php

namespace Dreamcc\Controller;

class Main {

    function __constructor() {
    }

    function setup($app) {

        $this->app = $app;

        $app->get('/', array($this, 'index'))->name('index');

        $app->post('/', array($this, 'login'))->name('login');

    }

    function index() {
        $contact = new \Dreamcc\Model\Contact();
        $this->app->render('consultant_panel.html');
    }

    function login() {
        $login = $app->request->post('login');
        $pass = $app->request->post('pass');

        $user = new \Dreamcc\Model\User();

        $this->app->render('consultant_panel.html');
    }

}

<?php

namespace Dreamcc\Controller;

class Main {

    var $app;

    function __construct($app, $view, $log, $contact, $user) {

        $this->app  = $app;
        $this->view = $view;
        $this->log  = $log;

        $this->contact = $contact;
        $this->user    = $user;
    }

    function setup() {

        $this->log->addDebug("Main Controller setup");
        $this->app->get('/', array($this, 'index'))->name('index');
        $this->app->post('/', array($this, 'login'))->name('login');
        $this->app->get('/logout', array($this, 'logout'))->name('logout');
    }

    function index() {

        $this->log->addDebug("index route");

        $contact = $this->contact;

        $body = $this->view->render('consultant_panel.html', array('user' => $this->user));
        $this->app->response->setBody($body);
    }

    function login() {

        $login = $this->app->request->post('login');
        $pass = $this->app->request->post('pass');

        $this->user->authorize($login, $pass);
        $this->app->response->redirect('/', 303);

        //$this->app->render('consultant_panel.html');
    }

    function logout() {

        $this->user->logout();
        $this->app->response->redirect('/', 303);

    }

}

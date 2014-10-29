<?php

namespace Dreamcc\Controller;

class Main {

    var $app;

    function __construct($app, $view, $log, $contact, $user, $dbconf, $bi) {

        $this->app  = $app;
        $this->view = $view;
        $this->log  = $log;

        $this->contact = $contact;
        $this->user    = $user;
        $this->dbconf  = $dbconf;
        $this->bi      = $bi;
    }

    function setup() {

        $this->log->addDebug("Main Controller setup");
        $this->app->get('/', array($this, 'index'))->name('index');
        $this->app->post('/', array($this, 'login'))->name('login');
        $this->app->get('/logout', array($this, 'logout'))->name('logout');

        $this->app->get('/contact', array($this, 'contact'))->name('contact');

        $this->app->get('/project/select', array($this, 'select_project'))->name('select_project');

        $this->app->get('/contact/search', array($this, 'contact_search_form'))
            ->name('contact_search_form');
        $this->app->post('/contact/search', array($this, 'contact_search_post'))
            ->name('contact_search_post');

        $this->app->get('/contact/rescheduled', array($this, 'contact_rescheduled'))
            ->name('contact_rescheduled');
        $this->app->get('/contact/left', array($this, 'contact_left'))
            ->name('contact_left');
        $this->app->get('/contact/reserve', array($this, 'contact_reserve'))
            ->name('contact_reserve');

        $this->app->get('/admin/prepare_views', array($this, 'prepare_views'))->name('prepare_views');

        $this->app->get('/bi', array($this, 'bi'))
            ->name('bi');


    }

    function index() {

        $this->log->addDebug("index route");

        $user    = $this->user->get(true);
        $contact = $this->contact;

        $body = $this->view->render('consultant_panel.html', array('user' => $user));
        $this->app->response->setBody($body);
    }

    function login() {

        $this->log->addDebug("login route");

        $login = $this->app->request->post('login');
        $pass  = $this->app->request->post('pass');

        $this->user->login($login, $pass);
        $url = $this->app->urlFor('index');
        $this->app->response->redirect($url, 303);

    }

    function logout() {

        $this->log->addDebug("logout route");
        $this->user->logout();
        $url = $this->app->urlFor('index');
        $this->app->response->redirect($url, 303);

    }

    function contact() {

        $this->log->addDebug("contact route");

        $user   = $this->user->get();
        $project   = $this->app->request->get('project');
        $result = $this->contact->get($user, $project); //add project parameter

        $body = $this->view->render('record.html', array(
            'user'   => $user,
            'result' => $result
        ));
        $this->app->response->setBody($body);
    }

    function select_project() {
        $this->log->addDebug("project selection route");

        $user       = $this->user->get();
        $projects   = $this->contact->getAvailableContactsPerProject();
        $body = $this->view->render('project_selection.html', array(
            'user' => $user,
            'projects' => $projects
        ));

        $this->app->response->setBody($body);
    }



    function contact_search_form() {

        $this->log->addDebug("contact_search route");

        $user = $this->user->get();

        $body = $this->view->render('search.html', array('user' => $user));
        $this->app->response->setBody($body);
    }

    function contact_search_post() {

        $this->log->addDebug("contact_search route");

        $user     = $this->user->get();
        $lastname = $this->app->request->post('lastname');
        $number   = $this->app->request->post('number');
        $result   = $this->contact->search($lastname, $number);

        $body = $this->view->render('search.html', array(
            'user'   => $user,
            'result' => $result
        ));
        $this->app->response->setBody($body);
    }

    function contact_rescheduled() {

        $this->log->addDebug("contact_rescheduled route");

        $user   = $this->user->get();
        $result = $this->contact->getRescheduled($user);

        $body = $this->view->render('rescheduled.html', array(
            'user'   => $user,
            'result' => $result
        ));
        $this->app->response->setBody($body);
    }

    function contact_left() {

        $this->log->addDebug("contact_left route");

        $user   = $this->user->get();
        $result = $this->contact->getLeft();

        $body = $this->view->render('left.html', array(
            'user'   => $user,
            'result' => $result
        ));
        $this->app->response->setBody($body);
    }

    function contact_reserve() {

        $this->log->addDebug("contact_reserve route");

        $user    = $this->user->get();
        $token   = $this->app->request->get('token');
        $result  = $this->contact->reserve($user, $token);

        $body = $this->view->render('record.html', array(
            'user'   => $user,
            'result' => $result
        ));
        $this->app->response->setBody($body);
    }

    function prepare_views() {
        //return;
        $msg    = $this->dbconf->createViews();
        var_dump($msg);
        $msg    = $this->dbconf->createReports();
        var_dump($msg);
    }

    function bi() {

        $user    = $this->user->get();
        $bi_data = $this->bi->get();

        $body = $this->view->render('bi.html', array(
            'user'    => $user,
            'bi_data' => $bi_data
        ));

        $this->app->response->setBody($body);
    }

}

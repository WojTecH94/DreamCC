<?php

namespace Dreamcc\Model;

class User {

    var $db;
    var $log;

    var $is_logged = false;
    var $login;

    var $users = array(
        "test"        => "test",
        "mlukasik"    => "ccliderzy",
        "mpaleczny"   => "ccliderzy",
        "mkoniuszewska" => "ccliderzy",
        "btumilowicz" => "ccliderzy",
        "psliwinska"  => "ccliderzy"
    );

    


    function __construct($db, $log, $cache, $config) {

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;
        
        $this->config = $config;
        $this->views_suffix = $this->config['project_config']['suffix'];
        
        $this->log->addDebug("user constructor");

        return $this;
    }

    function login($login, $pass) {

        if (isset($this->users[$login]) &&
            $pass == $this->users[$login]) {

            $_SESSION["user"] = $login;

            $this->log->addDebug("user logged in");
        }

        return $this;
    }

    function logout() {
        unset($_SESSION["user"]);
        return $this;
    }

    protected function _getValue($query, $param) {

        // if ($this->cache->get('user_' . $param)) {
        //     return $this->cache->get('user_' . $param);
        // }

        $this->log->addDebug("get value", array($query, $param));

        $result = $this->db->query($query);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // $this->cache->set('user_' . $param, $row[$param], 10);
            return $row[$param];
        }

        return 0;
    }

    function get($full = false) {

        if (isset($_SESSION["user"])) {

            $login = $_SESSION["user"];
            $data  = array();

            if ($full) {
                $data['contacts']       = $this->getContacts($login);
                $data['contacts_ticks'] = $this->getContactsTicks($data['contacts']);
                $data['succeeded']      = $this->getSucceeded($login);
                $data['tries']          = $this->getTries($login);
                $data['contacts_left']  = $this->getContactsLeft($login);
                $data['avg_time']       = $this->getAvgTime($login);

            }

            return array(
                'login'     => $login,
                'is_logged' => true,
                'data'      => $data
            );
        }

        return array('is_logged' => false);
    }

    function getTries($login) {

        // pobieranie ilośći wybranych rekordów w ostatniej godzinie przez obecnie zalogowanego użytkownika
        $login = $this->db->escape_string($login);

        $query = "SELECT tries FROM no_of_tries_" . $this->views_suffix . " WHERE operator = '{$login}'";

        return $this->_getValue($query, "tries");

    }

    function getSucceeded($login) {
        // pobieranie ilości przeprowadzonych rozmów w ciągu ostatniej godziny
        $login = $this->db->escape_string($login);
        $query = "SELECT succeeded FROM no_of_succeeded_" . $this->views_suffix . " WHERE operator ='{$login}'";

        return $this->_getValue($query, "succeeded");
    }

    function getContacts($login) {
        // pobieranie ilości rekordów przypisanych do konsultanta
        $login = $this->db->escape_string($login);
        $query = "SELECT COUNT(1) AS `contacts`
            FROM v_contacts_" . $this->views_suffix . " WHERE operator = '{$login}' GROUP BY operator";

        return $this->_getValue($query, "contacts");
    }

    function getContactsLeft($login) {

        // pobieranie ilości rekordów nieprzedzwonionych przez konsultanta
        $login = $this->db->escape_string($login);
        $query = "SELECT COUNT(1) AS `left`
            FROM v_left_contacts_" . $this->views_suffix . " WHERE operator = '{$login}' GROUP BY operator";

        return $this->_getValue($query, "left");
    }

    function getAvgTime($login) {

        // pobieranie średniego czasu potrzebnego do przeprowadzenia skutecznej rozmowy
        $login = $this->db->escape_string($login);
        $query = "SELECT avg_time FROM v_avg_timings_" . $this->views_suffix . " WHERE operator = '{$login}'";

        return $this->_getValue($query, "avg_time");
    }

    function getContactsTicks($contacts) {
        return ceil($contacts / 4);
    }


}

<?php

namespace Dreamcc\Model;

class User {

    var $db;
    var $log;

    var $is_logged = false;
    var $login;

    var $users = array(
        "test"        => "test",
        "psliwinska"  => "cctest",
        "mpaleczny"   => "ccliderzy",
        "btumilowicz" => "ccliderzy",
        "mlukasik"    => "ccliderzy",
        "mwitkos"     => "ccliderzy"
    );


    function __construct($db, $log, $cache) {

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;

        $this->log->addDebug("user constructor");

        if (isset($_SESSION["user"])) {

            $this->login     = $_SESSION["user"];
            $this->is_logged = true;
        }

        $this->log->addDebug("user status", array($this));

        return $this;
    }

    function authorize($login, $pass) {

        if (isset($this->users[$login]) &&
            $pass == $this->users[$login]) {

            $this->is_logged = true;
            $this->login     = $login;

            $this->log->addDebug("user logged in");
        }

        return $this;
    }

    function logout() {
        $this->is_logged = false;
        $this->login     = null;
    }


    function __destruct() {

        if ($this->is_logged === true) {
            return $_SESSION["user"] = $this->login;
        }

        unset($_SESSION["user"]);
    }

    protected function _getValue($query, $param) {

        if ($this->cache->get('user_' . $param)) {
            return $this->cache->get('user_' . $param);
        }

        $this->log->addDebug("get value", array($query, $param));

        $result = $this->db->query($query);

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->cache->set('user_' . $param, $row[$param], 10);
            return $row[$param];
        }

        return 0;
    }

    function getTries() {

        // pobieranie ilośći wybranych rekordów w ostatniej godzinie przez obecnie zalogowanego użytkownika
        $login = $this->db->escape_string($this->login);

        $query = "SELECT tries FROM no_of_tries WHERE operator = '{$login}'";

        return $this->_getValue($query, "tries");

    }

    function getSucceeded() {
        // pobieranie ilości przeprowadzonych rozmów w ciągu ostatniej godziny
        $login = $this->db->escape_string($this->login);
        $query = "SELECT succeeded FROM no_of_succeeded WHERE operator ='{$login}'";

        return $this->_getValue($query, "succeeded");
    }

    function getContacts() {
        // pobieranie ilości rekordów przypisanych do konsultanta
        $login = $this->db->escape_string($this->login);
        $query = "SELECT COUNT(1) AS `contacts`
            FROM v_contacts WHERE operator = '{$login}' GROUP BY operator";

        return $this->_getValue($query, "contacts");
    }

    function getContactsLeft() {

        // pobieranie ilości rekordów nieprzedzwonionych przez konsultanta
        $login = $this->db->escape_string($this->login);
        $query = "SELECT COUNT(1) AS `left`
            FROM v_left_contacts WHERE operator = '{$login}' GROUP BY operator";

        return $this->_getValue($query, "left");
    }

    function getAvgTime() {

        // pobieranie średniego czasu potrzebnego do przeprowadzenia skutecznej rozmowy
        $login = $this->db->escape_string($this->login);
        $query = "SELECT avg_time FROM v_avg_timings WHERE operator = '{$login}'";

        return $this->_getValue($query, "avg_time");
    }

    function getContactsTicks() {
        return ceil($this->getContacts() / 4);
    }


}

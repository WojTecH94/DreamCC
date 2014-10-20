<?php

namespace Dreamcc\Model;

class Contact {

    function __construct($db, $log, $cache) {

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;

        return $this;
    }

    function get($user) {
        $query = "CALL reserve_token('{$user['login']}')";
        $result = $this->db->query($query);
        return $result;
    }


    function getLeft() {

        $query = "SELECT * FROM v_left_contacts";
        $result = $this->db->query($query);

        return $result;
    }

    function getRescheduled($user) {
        $query = <<<SQL
        SELECT
            firstname, lastname,
            token, operator, project,
            IF(reservation_date= '' OR reservation_date IS NULL OR TIMESTAMPDIFF(MINUTE, reservation_date, CURRENT_TIMESTAMP()) >= 15,'wolny', 'zarezerwowany') AS reserved,
            status, attempt, contact_date,
            consultant, reschedule_date, notes
        FROM v_contacts
        WHERE consultant = '{$user['login']}' AND status = 'Inny termin'
        ORDER BY reschedule_date ASC
SQL;
        $result = $this->db->query($query);

        return $result;

    }

    function reserve($user, $token) {

        $query = "CALL reserve_defined_token('{$user['login']}','{$token}')";
        
        
        
        $result = $this->db->query($query);
        return $result;
    }

    function search($lastname, $number) {
        $query = <<<SQL
        SELECT
            firstname, lastname, token, number, operator, project,
            IF(reservation_date= '' OR reservation_date IS NULL OR TIMESTAMPDIFF(MINUTE, reservation_date, CURRENT_TIMESTAMP()) >= 15, 'wolny', 'zarezerwowany') AS reserved,
            status, attempt, contact_date -- , notes
        FROM v_contacts
        WHERE lastname LIKE '{$lastname}' OR number LIKE '{$number}'
SQL;

        $result = $this->db->query($query);

        return $result;
    }
}

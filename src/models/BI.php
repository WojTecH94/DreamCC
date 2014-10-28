<?php

namespace Dreamcc\Model;

class BI {

    var $db;
    var $log;


    function __construct($db, $log, $cache) {

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;

        return $this;
    }

    function getSucceeded() {
        $query  = "SELECT * FROM no_of_succeeded";
        $result = $this->db->query($query);
        return $result;
    }

    function getTimings() {
        $query  = "SELECT * FROM
          v_avg_timings";
        $result = $this->db->query($query);
        return $result;
    }

    function getLeft() {
        $query  = <<<SQL
            SELECT a.operator, a.left, b.all FROM
          (SELECT operator, COUNT(1) AS `left` FROM
            v_left_contacts GROUP BY operator) a
            INNER JOIN (SELECT operator, COUNT(1) AS `all` FROM v_contacts GROUP BY operator) b  ON a.operator=b.operator;
SQL;
        $result = $this->db->query($query);
        return $result;
    }

    function getRate() {
        $query  = <<<SQL
            SELECT cols.operator, cols.date,  calls.succeeded, worktime.worktime /60 AS `worktime`, calls.succeeded / (worktime.worktime/60) AS `tempo` FROM
          (SELECT date, operator FROM
              (SELECT DATE(contact_date) AS `date`
                     FROM v_contacts
                     WHERE status = 'Przeprowadzona'
                    Group By DATE(contact_date)) AS dates,
              (SELECT operator FROM v_contacts GROUP BY operator) AS operators) AS cols
                  LEFT JOIN
                  (SELECT operator, DATE(contact_date) AS `date`, TIMESTAMPDIFF(MINUTE, min(contact_date) , max(contact_date)) `worktime` FROM v_contacts
                GROUP BY operator, DATE(contact_date) ) AS worktime ON cols.date = worktime.date AND cols.operator = worktime.operator
                LEFT JOIN (SELECT operator, DATE(contact_date) AS `date`, count(1) AS succeeded
                 FROM v_contacts
                 WHERE status = 'Przeprowadzona'
                Group By operator,DATE(contact_date)) AS calls
            ON worktime.operator = calls.operator AND worktime.date = calls.date;
SQL;
        $result = $this->db->query($query);
        $return = array(
            "dates"     => array(),
            "operators" => array(),
        );

        while($row = $result->fetch_assoc()) {
            $data = array_intersect_key(
                $row,
                array_flip(array('succeeded', 'worktime', 'tempo'))
            );
            $return["dates"][$row["date"]] = 1;

            if (!$row['operator']) {
                continue;
            }

            $existing = (isset($return["operators"][$row["operator"]])) ?
                $return["operators"][$row["operator"]] : array();

            $return["operators"][$row["operator"]] = array_merge(
                $existing,
                array_combine(
                    array($row["date"] . "_s", $row["date"] . "_w", $row["date"] . "_t"),
                    $data
                )
            );
        }

        // $max_consultants = max(array_map(function ($row) {
        //     return count($row);
        // }, $return));
        //
        //
        // $return = array_map(function ($row) use ($max_consultants) {
        //     if (count($row) < $max_consultants) {
        //
        //     }
        //     return $row;
        // }, $return);

        return $return;
    }

    function get() {
        return array(
            "succeeded" => $this->getSucceeded(),
            "timings"   => $this->getTimings(),
            "left"      => $this->getLeft(),
            "rate"      => $this->getRate()
        );
    }
}

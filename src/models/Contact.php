<?php

namespace Dreamcc\Model;

class Contact {

    

    function __construct($db, $log, $cache, $config) {

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;
        
        $this->config = $config;
        $this->views_suffix = $this->config['project_config']['suffix'];

        return $this;
    }

    function get($user, $project) {
		//blokada
		$que0 = <<<SQL
			START TRANSACTION
SQL;
		$p0 = $this->db->query($que0);
		//WL: Moj posob na konkurencje: w stworzonej tabeli op_id_tab mamy kolumne project (nazwa projektu) oraz op_id (indeks malejacy od 10 do 0) -> line: 49
		$que1 = <<<SQL
			SELECT op_id from op_id_tab where project = '{$project}' FOR UPDATE
SQL;
		$p1 = $this->db->query($que1);
		$wier = mysqli_fetch_assoc($p1);
		$op_id = ($wier['op_id'])% 10;
		$que2 = <<<SQL
                UPDATE op_id_tab SET op_id = (op_id -1) WHERE project = '{$project}'
SQL;
		$p2 = $this->db->query($que2);
		//koniec blokady	
        if ($p1 and $p2) {
			$que3 = <<<SQL
			COMMIT
SQL;
		} else {
			$que3 = <<<SQL
			ROLLBACK
SQL;
		}
		$p3 = $this->db->query($que3);
        //czy kontakty są przypisane do konstultantów
        if($this->config['project_config']['defined_user']){
            $query1 = <<<SQL
                SELECT distinct token, firstname, lastname, status, attempt, project, sid, operator_gid, operator_qid  FROM v_available_contacts_{$this->views_suffix} WHERE project = '{$project}' AND operator = '{$user['login']}' LIMIT {$op_id},1
SQL;
        }
        else{
			//a tutaj wyszukujemy rekord o numerze op_id co powoduje ze kazde z wywolan "pobierz rekord" bedzie bralo inny kontakt
            $query1 = <<<SQL
                SELECT distinct token, firstname, lastname, status, attempt, project, sid, operator_gid, operator_qid  FROM v_available_contacts_{$this->views_suffix} WHERE project = '{$project}' LIMIT {$op_id},1
SQL;
        }
        
        $r1 = $this->db->query($query1);
        
        //fetch variables needed for the next queries
        $row = mysqli_fetch_assoc($r1);
        $token = $row['token'];
        $project = $row['project'];
        $sid = $row['sid'];
        
        $query2 = <<<SQL
                UPDATE lime_survey_{$sid} SET submitdate = null, lastpage = null WHERE token = '{$token}'
SQL;
        $r2 = $this->db->query($query2);

        $query3 = <<<SQL
                UPDATE lime_tokens_{$sid} SET attribute_1 = '{$user['login']}', attribute_2 = CURRENT_TIMESTAMP() WHERE token = '{$token}'
SQL;
        $r3 = $this->db->query($query3);
		
        return $row;
    }


    function getLeft() {

        $query = "SELECT * FROM v_left_contacts_" . $this->views_suffix;
        $result = $this->db->query($query);

        return $result;
    }
    
    function getAvailableContactsPerProject(){
        
        $query = "SELECT project, sid, operator_gid, operator_qid, COUNT(1) as cnt  FROM v_available_contacts_" . $this->views_suffix . " GROUP BY project, sid, operator_gid, operator_qid";
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
            consultant, reschedule_date -- , notes
        FROM v_contacts_{$this->views_suffix}
        WHERE consultant = '{$user['login']}' AND status = 'Inny termin'
        ORDER BY reschedule_date ASC
SQL;
        $result = $this->db->query($query);

        return $result;

    }

    function reserve($user, $token) {
				$query0 = <<<SQL
		START TRANSACTION
SQL;

        $query1 = <<<SQL
                SELECT token, firstname, lastname, status, attempt, project, sid, operator_gid, operator_qid  FROM v_contacts_{$this->views_suffix} WHERE token = '{$token}' FOR UPDATE
SQL;
        $r1 = $this->db->query($query1);
        
        //fetch variables needed for the next queries
        $row = mysqli_fetch_assoc($r1);
        $token = $row['token'];
        $project = $row['project'];
        $sid = $row['sid'];
        
        $query2 = <<<SQL
                UPDATE lime_survey_{$sid} SET submitdate = null, lastpage = null WHERE token = '{$token}'
SQL;
        $r2 = $this->db->query($query2);

        $query3 = <<<SQL
                UPDATE lime_tokens_{$sid} SET attribute_1 = '{$user['login']}', attribute_2 = CURRENT_TIMESTAMP() WHERE token = '{$token}'
SQL;
        $r3 = $this->db->query($query3);
        if ($r1 and $r2 and $r3) {
		$query4 = <<<SQL
		COMMIT
SQL;
		} else {
		$query4 = <<<SQL
		ROLLBACK
SQL;
		}
				$r4 = $this->db->query($query4);
        
        return $row;
    }

    function search($lastname, $number) {
        $query = <<<SQL
        SELECT
            firstname, lastname, token, number, operator, project,
            IF(reservation_date= '' OR reservation_date IS NULL OR TIMESTAMPDIFF(MINUTE, reservation_date, CURRENT_TIMESTAMP()) >= 15, 'wolny', 'zarezerwowany') AS reserved,
            status, attempt, contact_date , notes, reschedule_date
        FROM v_contacts_{$this->views_suffix}
        WHERE lastname LIKE '{$lastname}' OR number LIKE '{$number}'
SQL;

        $result = $this->db->query($query);

        return $result;
    }
}

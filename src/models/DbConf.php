<?php
/* this model is used to preconfigure the database views and procedures*/

namespace Dreamcc\Model;

class DbConf {

    var $projects = array();
    
    //contacts availability setup
    var $reservationTime = 15; //minutes
    var $notRespondingDelay = 1; //hours
    var $busyDelay = 15; //minutes
    var $respondentMissingDelay = 3; //hours
    
    function __construct($db, $log, $cache, $config) {

        
        // $this->config = $config;
        //$this->views_suffix = "liderzy4"; //$this->config['views_config']['suffix'] ;// suffix dodawany do widoków, aby odróżnić projekty

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;

        $this->config = $config;
        $this->views_suffix = $this->config['project_config']['suffix'];
        
        
        $this->projects[] = array(
            "sid" => '429911',
            "gid" => '56',
            "gid2" => '57',
            "qids" => array(
               "contactDate" => '650',
               "attempt" => '652',
               "status" => '651',
               "rescheduleDate" => '653',
               "consultant" => '655',
               "notes" => '656'
            ),
            "project" => 'CC_test' 
        );
        

        return $this;
    }


    function getProjects(){

        return $this->projects;
    }


    function createViews(){
       $msg[0] = $this->dropViews();
       $msg[1] = $this->createContactsView();
       $msg[2] = $this->createAvailableContactsView();
       $msg[3] = $this->createLeftView();
	   $msg[4] = $this->createControllTable();
	   $msg[5] = $this->changeCharsetForIndexing();
       return $msg;
    }

    function dropViews(){
        
        $query = "DROP VIEW no_of_succeeded_" . $this->views_suffix;
        $result[0] = $this->db->query($query);
        $query = "DROP VIEW no_of_tries_" . $this->views_suffix;
        $result[1] = $this->db->query($query);
        $query = "DROP VIEW v_available_contacts_" . $this->views_suffix;
        $result[2] = $this->db->query($query);
        $query = "DROP VIEW v_avg_timings_" . $this->views_suffix;
        $result[3] = $this->db->query($query);
        $query = "DROP VIEW v_contacts_" . $this->views_suffix;
        $result[4] = $this->db->query($query);
        $query = "DROP VIEW v_left_contacts_" . $this->views_suffix;
        $result[5] = $this->db->query($query);
        $query = "DROP VIEW v_timings_" . $this->views_suffix;
        $result[6] = $this->db->query($query);
        return $result;
    }

    //creates view v_contacts
    function createContactsView(){

        //fix for the token attributes fuckUp
        $operator_attribute = "attribute_1";
        $reservation_date_attribute = "attribute_2";
        $number_attribute = "attribute_3";


        //first query is a bit different
        $query = <<<SQL
        CREATE VIEW v_contacts_{$this->views_suffix} AS
            SELECT token.firstname, token.lastname, token.token,
                    token.{$operator_attribute} AS operator, -- login operatora/konsulatna
                    token.{$reservation_date_attribute} AS reservation_date, -- data i godzina rezerwacji rekordu
                    token.{$number_attribute} AS number, -- numer telefonu
                    survey.startdate, -- data rozpoczęcia ankiety wg. lime
                    -- poniższe idki są do zmiany/generowania przy zmianie projektu
                    status.answer AS status,
                    attempt.answer AS attempt,
                    survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['contactDate']} AS contact_date, -- data kontaktu
                    survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['rescheduleDate']} AS reschedule_date, -- na kiedy przełożono rozmowę
                    survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['consultant']} AS consultant,  -- konsultant z odpowiedzi, po wypełnieniu ankiety powinien być ten sam co operator
                    survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid2']}X{$this->projects[0]['qids']['notes']} AS notes -- uwagi po rozmowie
                    ,'{$this->projects[0]['project']}' AS project
                    ,'{$this->projects[0]['sid']}' AS sid
                    ,'{$this->projects[0]['gid']}' AS operator_gid
                    ,'{$this->projects[0]['qids']['consultant']}' AS operator_qid
              FROM lime_tokens_{$this->projects[0]['sid']} token
                  LEFT JOIN lime_survey_{$this->projects[0]['sid']} survey ON token.token = survey.token
                  LEFT JOIN lime_answers status ON status.qid = {$this->projects[0]['qids']['status']} AND survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['status']} = status.code
                  LEFT JOIN lime_answers attempt ON attempt.qid = {$this->projects[0]['qids']['attempt']} AND survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['attempt']} = attempt.code
SQL;

        //concatenate project views
        for($i = 1; $i < count($this->projects); $i++){
            $currQuery = <<<SQL
                UNION ALL
                    SELECT token.firstname, token.lastname, token.token,
                            token.{$operator_attribute} AS operator, -- login operatora/konsulatna
                            token.{$reservation_date_attribute} AS reservation_date, -- data i godzina rezerwacji rekordu
                            token.{$number_attribute} AS number, -- numer telefonu
                            survey.startdate, -- data rozpoczęcia ankiety wg. lime
                            -- poniższe idki są do zmiany/generowania przy zmianie projektu
                            status.answer AS status,
                            attempt.answer AS attempt,
                            survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['contactDate']} AS contact_date, -- data kontaktu
                            survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['rescheduleDate']} AS reschedule_date, -- na kiedy przełożono rozmowę
                            survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['consultant']} AS consultant,  -- konsultant z odpowiedzi, po wypełnieniu ankiety powinien być ten sam co operator
                            survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid2']}X{$this->projects[$i]['qids']['notes']} AS notes -- uwagi po rozmowie
                            ,'{$this->projects[$i]['project']}' AS project
                            ,'{$this->projects[$i]['sid']}' AS sid
                            ,'{$this->projects[$i]['gid']}' AS operator_gid
                            ,'{$this->projects[$i]['qids']['consultant']}' AS operator_qid
                      FROM lime_tokens_{$this->projects[$i]['sid']} token
                          LEFT JOIN lime_survey_{$this->projects[$i]['sid']} survey ON token.token = survey.token
                          LEFT JOIN lime_answers status ON status.qid = {$this->projects[$i]['qids']['status']} AND survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['status']} = status.code
                          LEFT JOIN lime_answers attempt ON attempt.qid = {$this->projects[$i]['qids']['attempt']} AND survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['attempt']} = attempt.code
SQL;
              $query = $query . $currQuery;
        }
        $query = $query . ' ORDER BY reservation_date';
        //var_dump($query);
        $result = $this->db->query($query);
        return $result;
    }

    //creates view v_available_contacts
    function createAvailableContactsView(){

        $query = <<<SQL
                CREATE VIEW v_available_contacts_{$this->views_suffix} AS
                SELECT * FROM v_contacts_{$this->views_suffix}
                      WHERE
                            (attempt IN ('Pierwsza','Druga','') OR attempt IS NULL) -- (Próba dotarcia IN ('Pierwsza', 'Druga',) OR Próba dotarcia is  NULL)
                            AND
                            (reservation_date is NULL OR reservation_date = '' OR TIMESTAMPDIFF(MINUTE, reservation_date,CURRENT_TIMESTAMP()) >= {$this->reservationTime}) -- data rezerwacji > 15 min
                            AND (
                                  (status IN ('Nie odbiera','Automatyczna sekretarka') AND TIMESTAMPDIFF(HOUR, contact_date, CURRENT_TIMESTAMP()) >= {$this->notRespondingDelay}) -- (Status IN (Nie odbiera,  Automatyczna sekretarka) AND Data kontaktu > 1h)
                                  OR
                                  (status = 'Zajęty' AND TIMESTAMPDIFF(MINUTE, contact_date,CURRENT_TIMESTAMP()) >= {$this->busyDelay}) -- (Status = Zajęty AND Data kontaktu > 15 min)
                                  OR
                                  (status = 'Numer dobry, brak klienta' AND TIMESTAMPDIFF(HOUR, contact_date, CURRENT_TIMESTAMP()) >= {$this->respondentMissingDelay}) -- (Status = Numer dobry AND Data kontaktu > 3h)
                                  OR
                                  (status is NULL OR status = '')
                                )
SQL;
        $result = $this->db->query($query);
        return $result;
    }

    //creates v_left_contacts view
    function createLeftView(){
        $query = <<<SQL
                CREATE VIEW v_left_contacts_{$this->views_suffix} AS
                    SELECT * FROM v_contacts_{$this->views_suffix}
                          WHERE
                                (attempt IN ('Pierwsza','Druga','') OR attempt IS NULL) -- (Próba dotarcia IN ('Pierwsza', 'Druga',) OR Próba dotarcia is  NULL)
                                AND (
                                      (status IN ('Inny termin','Nie odbiera','Automatyczna sekretarka','Zajęty','Numer dobry, brak klienta','')) -- (Status IN (Nie odbiera,  Automatyczna sekretarka))
                                      OR
                                      (status is NULL)
                                    )
SQL;
        $result = $this->db->query($query);
        return $result;
    }



    function createReports(){
        //tries report
        $query = <<<SQL
                CREATE VIEW no_of_tries_{$this->views_suffix} AS
                       SELECT operator, count(1) AS tries FROM v_contacts_{$this->views_suffix} WHERE  TIMESTAMPDIFF(HOUR, contact_date, CURRENT_TIMESTAMP()) < 1  Group By operator
SQL;
        $result[0] = $this->db->query($query);

        //success report
        $query = <<<SQL
                CREATE VIEW no_of_succeeded_{$this->views_suffix} AS
                SELECT operator, count(1) AS succeeded
                  FROM v_contacts_{$this->views_suffix}
                  WHERE  ( TIMESTAMPDIFF(HOUR, contact_date, CURRENT_TIMESTAMP()) < 1 ) AND status = 'Przeprowadzona'
                  Group By operator
SQL;
        $result[1] = $this->db->query($query);

        //timing report
        $query = <<<SQL
               CREATE VIEW v_timings_{$this->views_suffix} AS
                    SELECT answers.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['consultant']} AS operator, interviewtime
                        FROM lime_survey_{$this->projects[0]['sid']}_timings timings
                             INNER JOIN lime_survey_{$this->projects[0]['sid']} answers ON timings.id = answers.id
                             WHERE answers.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['status']} = 'A1'
SQL;


        for($i = 1; $i < count($this->projects); $i++){
            $currQuery = <<<SQL
                UNION ALL
                   SELECT answers.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['consultant']} AS operator, interviewtime
                        FROM lime_survey_{$this->projects[$i]['sid']}_timings timings
                             INNER JOIN lime_survey_{$this->projects[$i]['sid']} answers ON timings.id = answers.id
                             WHERE answers.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['status']} = 'A1'
SQL;
         $query = $query . $currQuery;
        }
        $result[2] = $this->db->query($query);

        //avg timings report
        $query = <<<SQL
                CREATE VIEW v_avg_timings_{$this->views_suffix} AS
                    SELECT operator, ROUND(AVG(interviewtime/60),2) AS avg_time FROM v_timings_{$this->views_suffix} GROUP BY operator
SQL;
        $result[3] = $this->db->query($query);
        return $result;
   }

    function createControllTable(){
        //table to controll concurency
        $query = <<<SQL
			CREATE TABLE IF NOT EXISTS op_id_tab (
			  project varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
			  op_id int(11) unsigned NOT NULL DEFAULT '4294967295',
			  PRIMARY KEY (`project`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
SQL;
		$result[1] = $this->db->query($query);
		//and record for the current project
        $query = <<<SQL
			INSERT IGNORE INTO op_id_tab ( project ) VALUES ('{$this->projects[0]['project']}')
SQL;
        $result[2] = $this->db->query($query);
        return $result;
   }
   function changeCharsetForIndexing(){
        //changing charset for survey table to enable indexing (great optimalization)
        $query = <<<SQL
			ALTER TABLE lime_survey_{$this->projects[0]['sid']}
			MODIFY COLUMN `token` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL;
SQL;
		$result[1] = $this->db->query($query);
        return $result;
   }


}

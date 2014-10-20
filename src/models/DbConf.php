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
    
    
    
    
    function __construct($db, $log, $cache) {

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;

        //multi projects configuration - don't know why I can't set it directly after class declaration;
        $this->projects[0] = array(
            "sid" => '777',
            "gid" => '1416',
            "qids" => array(
               "status" => '20361',
               "attempt" => '20363',
               "contactDate" => '20361',
               "rescheduleDate" => '20366',
               "consultant" => '20365' 
            ),
            "project" => 'baza1'
        );
        
        $this->projects[1] = array(
            "sid" => '888',
            "gid" => '1417',
            "qids" => array(
               "status" => '20368',
               "attempt" => '20369',
               "contactDate" => '20367',
               "rescheduleDate" => '20372',
               "consultant" => '20371' 
            ),
            "project" => 'baza2'
        );
        
        $this->projects[2] = array(
            "sid" => '999',
            "gid" => '1418',
            "qids" => array(
               "status" => '20374',
               "attempt" => '20375',
               "contactDate" => '20373',
               "rescheduleDate" => '20378',
               "consultant" => '20377' 
            ),
            "project" => 'baza3'
        );
        
        return $this;
    }
    
    function createViews(){
       $msg[0] = $this->createContactsView();
       $msg[1] = $this->createAvailableContactsView();
       return $msg;
    }
    
    //creates view v_contacts
    function createContactsView(){
        
        //first query is a bit different
        $query = <<<SQL
        CREATE VIEW v_contacts AS
            SELECT token.firstname, token.lastname, token.token, 
                    token.attribute_1 AS operator, -- login operatora/konsulatna
                    token.attribute_2 AS reservation_date, -- data i godzina rezerwacji rekordu  
                    token.attribute_3 AS number, -- numer telefonu
                    survey.startdate, -- data rozpoczęcia ankiety wg. lime
                    -- poniższe idki są do zmiany/generowania przy zmianie projektu
                    status.answer AS status,
                    attempt.answer AS attempt,
                    survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['contactDate']} AS contact_date, -- data kontaktu
                    survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['rescheduleDate']} AS reschedule_date, -- na kiedy przełożono rozmowę
                    survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['consultant']} AS consultant  -- konsultant z odpowiedzi, po wypełnieniu ankiety powinien być ten sam co operator
                    -- survey.{$this->projects[0]['sid']}X{$this->projects[0]['gid']}X{$this->projects[0]['qids']['contactDate']} AS notes -- uwagi po rozmowie
                    ,'{$this->projects[0]['project']}' AS project
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
                            token.attribute_1 AS operator, -- login operatora/konsulatna
                            token.attribute_2 AS reservation_date, -- data i godzina rezerwacji rekordu  
                            token.attribute_3 AS number, -- numer telefonu
                            survey.startdate, -- data rozpoczęcia ankiety wg. lime
                            -- poniższe idki są do zmiany/generowania przy zmianie projektu
                            status.answer AS status,
                            attempt.answer AS attempt,
                            survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['contactDate']} AS contact_date, -- data kontaktu
                            survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['rescheduleDate']} AS reschedule_date, -- na kiedy przełożono rozmowę
                            survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['consultant']} AS consultant  -- konsultant z odpowiedzi, po wypełnieniu ankiety powinien być ten sam co operator
                            -- survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['contactDate']} AS notes -- uwagi po rozmowie
                            ,'{$this->projects[$i]['project']}' AS project
                      FROM lime_tokens_{$this->projects[$i]['sid']} token
                          LEFT JOIN lime_survey_{$this->projects[$i]['sid']} survey ON token.token = survey.token
                          LEFT JOIN lime_answers status ON status.qid = {$this->projects[$i]['qids']['status']} AND survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['status']} = status.code
                          LEFT JOIN lime_answers attempt ON attempt.qid = {$this->projects[$i]['qids']['attempt']} AND survey.{$this->projects[$i]['sid']}X{$this->projects[$i]['gid']}X{$this->projects[$i]['qids']['attempt']} = attempt.code    
SQL;
              $query = $query . $currQuery;            
        }
        $query = $query . 'ORDER BY reservation_date';
        $result = $this->db->query($query);
        return $result;
    }
    
    //creates view v_available_contacts
    function createAvailableContactsView(){
        
        $query = <<<SQL
                CREATE VIEW v_available_contacts AS
                SELECT * FROM v_contacts
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
    
    function createReports(){
        //tries report
        $query = <<<SQL
                CREATE VIEW no_of_tries AS
                       SELECT operator, count(1) AS tries FROM v_contacts WHERE  TIMESTAMPDIFF(HOUR, contact_date, CURRENT_TIMESTAMP()) < 1  Group By operator
SQL;
        $result[0] = $this->db->query($query);
        
        //success report
        $query = <<<SQL
                CREATE VIEW no_of_succeeded AS
                SELECT operator, count(1) AS succeeded
                  FROM v_contacts 
                  WHERE  ( TIMESTAMPDIFF(HOUR, contact_date, CURRENT_TIMESTAMP()) < 1 ) AND status = 'Przeprowadzona'
                  Group By operator
SQL;
        $result[1] = $this->db->query($query);
        
        //timing report
        $query = <<<SQL
               CREATE VIEW v_timings AS
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
                CREATE VIEW v_avg_timings AS
                    SELECT operator, ROUND(AVG(interviewtime/60),2) AS avg_time FROM v_timings
SQL;
        $result[3] = $this->db->query($query);
        return $result;
   }
   
   
   function createProcedures(){
       $query = <<<SQL
               CREATE PROCEDURE reserve_token(user varchar(50), project varchar(50))
                BEGIN
                  SELECT @tok := token AS tok, firstname, lastname, status, attempt  FROM v_available_contacts WHERE project = @project LIMIT 1;
                  UPDATE lime_survey_254117 SET submitdate = null, lastpage = null WHERE token = @tok;
                  UPDATE lime_tokens_254117 SET attribute_1 = user, attribute_2 = CURRENT_TIMESTAMP() WHERE token = @tok;
                --  SELECT @tok AS token;
                END;
SQL;
   }

    
}


<?php
/* this model is used to preconfigure the database views and procedures*/

namespace Dreamcc\Model;

class DbConf {
    
    function __construct($db, $log, $cache) {

        $this->db    = $db;
        $this->log   = $log;
        $this->cache = $cache;

        return $this;
    }
    
    function makeViews(){
        
        $sid = '777';
        $gid = '1416';
        $qids = array(
           "status" => '20361',
           "attempt" => '20363',
           "contactDate" => '20361',
           "rescheduleDate" => '20366',
           "consultant" => '20365' 
        );
        $query = <<<SQL
        ALTER VIEW v_contacts_test AS
            SELECT token.firstname, token.lastname, token.token, 
                    token.attribute_1 AS operator, -- login operatora/konsulatna
                    token.attribute_2 AS reservation_date, -- data i godzina rezerwacji rekordu  
                    token.attribute_3 AS number, -- numer telefonu
                    survey.startdate, -- data rozpoczęcia ankiety wg. lime
                    -- poniższe idki są do zmiany/generowania przy zmianie projektu
                    status.answer AS status,
                    attempt.answer AS attempt,
                    survey.{$sid}X{$gid}X{$qids['contactDate']} AS contact_date, -- data kontaktu
                    survey.{$sid}X{$gid}X{$qids['rescheduleDate']} AS reschedule_date, -- na kiedy przełożono rozmowę
                    survey.{$sid}X{$gid}X{$qids['consultant']} AS consultant  -- konsultant z odpowiedzi, po wypełnieniu ankiety powinien być ten sam co operator
                    -- survey.{$sid}X{$gid}X{$qids['contactDate']} AS notes -- uwagi po rozmowie
              FROM lime_tokens_{$sid} token
                  LEFT JOIN lime_survey_{$sid} survey ON token.token = survey.token
                  LEFT JOIN lime_answers status ON status.qid = {$qids['status']} AND survey.{$sid}X{$gid}X{$qids['status']} = status.code
                  LEFT JOIN lime_answers attempt ON attempt.qid = {$qids['attempt']} AND survey.{$sid}X{$gid}X{$qids['attempt']} = attempt.code
            ORDER BY reservation_date
SQL;
        
        $result = $this->db->query($query);
        
        return $result;
    }
}


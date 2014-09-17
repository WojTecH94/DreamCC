<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of contact
 *
 * @author mostafil
 */
//include 'config.php';

class contact {
    //put your code here
    public $token;
    public $firstname;
    public $lastname;
    public $operator;
    public $reservation_date;
    public $status;
    public $attempt;
    function __construct($token, $firstname, $lastname, $status, $attempt) 
    {
        $this->token = $token;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->status = $status;
        $this->attempt = $attempt;
        
    }
    
    //funkcja generująca link do ankiety
    public function gen_link($user){ 
       // $nextAttempt = $this->attempt++; //inkrementacja próby dotarcia
        $link = LIME_ADDRESS . "sid/" . SID . "/token/" . $this->token . "/lang//newtest/Y" . "?" . OPERATOR_QUESTION_ID . "=". $user ."&token=".$this->token;
        
        return $link;
    }
    
    public function present(){
        $presentation = "<p>Token: " . $this->token . "<br/>" . "Imię: " . $this->firstname . "<br/>" . "Nazwisko: " . $this->lastname . "<br/>" . "Status: " . $this->status . "<br/>" . "Próba dotarcia: " .$this->attempt;
        return $presentation;
    }
}

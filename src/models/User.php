<?php

namespace Dreamcc\Model;

class User {

    var $is_logged = false;

    function __constructor() {
        if ($_SESSION["user"]) {
            $this->name      = $_SESSION["user"]
            $this->is_logged = true;
        }
    }

}

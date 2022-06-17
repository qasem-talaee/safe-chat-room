<?php
function hash_pass($pass){
    $str = str_split($pass);
    $pass = "!pkkds323" . $str[0] . "dslij821?" . $str[1] . "fdgdiu399@#" . $str[2] . "lijkb!?cr8" . implode("", array_slice($str, 3));
    $pass = hash('sha256', $pass);
    return $pass;
}
?>
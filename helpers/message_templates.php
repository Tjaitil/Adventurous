<?php
$message_templates = array();
$message_templates['adventure_request'] =
    nl2br("Hello! \n
    I want to join your adventure! \n
    <a href='/adventures'> Click here to respond </a>\n
    
    Regards \n" . 
    $this->username);
$message_templates['adventure_invite'] =
    nl2br("Hello! \n
    I want you to join my adventure! \n
    <a href='/adventures'> Click here to respond </a> \n
    Regards \n" . 
    $this->username);
?>
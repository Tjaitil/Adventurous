<?php
$message_templates = array();
$message_templates['adventure_request'] =
    nl2br("Hello! \n
    I want to join your adventure! \n
    Head over to adventure base to respond </a>\n
    
    Regards \n" . 
    $this->username);
$message_templates['adventure_invite'] =
nl2br("Hello!\rI want you to join my adventure!\nHead over to adventure base in the nearest city to respond \nRegards\n" . 
$this->username);
?>
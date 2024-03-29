<?php

namespace App\libs;

class session
{
    public $status = 'neutral';

    function __construct()
    {
        session_start();
        if (App::getInstance()->getIsMocking()) {
            $_SESSION['username'] = $_ENV['TEST_USERNAME'];
        }
    }
    public function setSession($username, $loggedin)
    {
        $_SESSION['username'] = $username;
        $_SESSION['loggedin'] = true;
        $_SESSION['session_id'] = $this->generateID();
        $_SESSION['profiency'] = true;
        $_SESSION['log'][] = "Welcome to Adventurous, " . $username . "!";
        $this->status = true;
    }
    public function validateLogin()
    {
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            $this->status = false;
        }
    }
    private function generateID()
    {
        $id = null;
        for ($i = 0; $i < 6; $i++) {
            $id .= rand(1, 9);
        }
        return $id;
    }
    public function destroy()
    {
        if (isset($_SESSION['outdatedSessionID'])) {
            $_SESSION = array();
            $_SESSION['outdatedSessionID'] = true;
        } else {
            session_unset();
            session_destroy();
        }
        header("Location: /login");
        exit;
    }
}

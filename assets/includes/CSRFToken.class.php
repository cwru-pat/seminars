<?php

class CSRFToken
{
    private $token;
    public function __construct()
    {
        if (!isset($_SESSION['CSRFToken'])) {
            $_SESSION['CSRFToken'] = md5(microtime());
        }
        $this->token = $_SESSION['CSRFToken'];
    }
    public function getToken()
    {
        return $this->token;
    }
    public function validateToken($token)
    {
        return $token == $this->token;
    }
}
?>

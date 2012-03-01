<?php
class CryptHelper
{
    protected static $salt = '....';

    public function getConfirmationCode()
    {
        return sha1(uniqid(self::$salt, TRUE));
    }
}

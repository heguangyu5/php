<?php
class CryptHelper
{
    protected static $salt = '....';

    public static function getConfirmationCode()
    {
        return sha1(uniqid(self::$salt, TRUE));
    }
}

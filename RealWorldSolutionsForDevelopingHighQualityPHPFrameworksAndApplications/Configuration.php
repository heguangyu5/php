<?php
class Configuration
{
    protected static $values = array();

    public static function init(array $values)
    {
        self::$values = $values;
    }

    public static function get($key) 
    {
        if (!isset(self::$values[$key])) {
            throw new Exception('No suck key');
        }
        return self::$values[$key];
    }
}

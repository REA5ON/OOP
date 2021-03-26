<?php

class Session {
    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }

    public static function delete($name) {
        if(self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    public static function get($name) {
        return $_SESSION[$name];
    }

    public static function flash($name, $string = '') {
        if(self::exists($name) && self::get($name) !== '') {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::put($name, $string);
        }
    }

//    public static function set_flash_message($name, $message)
//    {
//        $_SESSION[$name] = $message;
//    }
//
//    public static function display_flash_message($name)
//    {
//        if (isset($_SESSION[$name])) {
//            echo "<div class=\"alert alert-{$name} text-dark\" role=\"alert\">{$_SESSION[$name]}</div>";
//            unset($_SESSION[$name]);
//        }
//    }
}
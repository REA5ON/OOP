<?php

class Token {
    //Создает новый токен (вставляет в форму)
    public static function generate() {
        //Записывает в сессию ключ(полученый из $_GLOBALS['config']) => значение ('token' => uniqid)
        return Session::put(Config::get('session.token_name'), md5(uniqid()));
    }


    //Проверяет сушествует ли такой ключ в сессии
    public static function check($token) {
        $tokenName = Config::get('session.token_name');

        //Если в сессии есть имя 'token' и $_POST['token'] == $_SESSION['token']
        if (Session::exists($tokenName) && $token == Session::get($tokenName)) {
            Session::delete($tokenName);
            return true;
        }

        return false;
    }
}

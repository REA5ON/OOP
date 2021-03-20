<?php

class User {
    private $db, $data, $session_name, $isLoggedIn, $cookieName;

    public function __construct($user = null) {
        $this->db = Database::getInstance();
        $this->cookieName = Config::get('cookie.cookie_name');
        $this->session_name = Config::get('session.user_session');


        //Если я ничего не передал в конструкт
        if (!$user) {
            //Если присутствует в сесии данные то получаем юзера по id / email
            if (Session::exists($this->session_name)) {
                $user = Session::get($this->session_name); //id
                if ($this->find($user)) {
                    $this->isLoggedIn = true;
                } else {
                    //Logout
                }
            }
        } else {
            $this->find($user);
        }
    }


    //создаем пользователя
    public function create($fields = []) {
        $this->db->insert('users', $fields);
    }

    public function login ($email = null, $password = null, $remember = false) {
        //Если не передали данные и текущий пользователь существует
        if (!$email && !$password && $this->exists()) {
            //Записываем в сессию текущего пользователя
            Session::put($this->session_name, $this->data()->id);
        } else {
            $user = $this->find($email);
            if ($user) {
                if (password_verify($password, $this->data()->password)) {
                    Session::put($this->session_name, $this->data()->id);

                    //Создаем хэш
                    if ($remember) {
                        $hash = hash('sha256', uniqid());

                        //Пытаемся найти текущий хэш пользователя
                        $hashCheck = $this->db->get('user_sessions', ['user_id', '=', $this->data()->id]);

                        //Если нет в базе то создаем
                        if (!$hashCheck->count()) {
                            $this->db->insert('user_sessions', [
                                'user_id' => $this->data()->id,
                                'hash' => $hash
                            ]);
                        } else {
                            //Если есть запись в БД то возвращаем её
                            $hash = $hashCheck->first()->hash;
                        }

                        //Записываем в кукки хэш
                        Cookie::put($this->cookieName, $hash, Config::get('cookie.cookie_expiry'));
                    }

                    return true;
                }
            }
        }
        return false;
    }

    //Ищем пользователя по имейлу / id
    public function find ($value = null) {
        if (is_numeric($value)) {
            //Получаем пользователя по id
            $this->data = $this->db->get('users',['id', '=', $value])->first();
        } else {
            //Получаем пользователя по имейл
            $this->data = $this->db->get('users',['email', '=', $value])->first();
        }
        //Если БД отдает данные - записываем в переменную $data
        if ($this->data) {
            return true;
        }

        return false;
    }

    public function data() {
        return $this->data;
    }


    public function isLoggedIn () {
        return $this->isLoggedIn;
    }

    public function logout() {
        $this->db->delete('user_sessions', ['user_id', '=', $this->data()->id]);
        Session::delete($this->session_name);
        Cookie::delete($this->cookieName);
    }

    public function exists() {
        return (!empty($this->data())) ? true : false;
    }

    public function update($fields = [], $id = null) {
        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;
        }

        $this->db->update('users', $id, $fields);
    }


    public function hasPermissions($key = null) {
        if ($key) {
            $group = $this->db->get('groups', ['id', '=', $this->data()->group_id]);

            if ($group->count()) {
                $permissions = $group->first()->permissions;
                $permissions = json_decode($permissions, true);

                if (isset($permissions[$key])) {
                    return true;
                }

            }
        }
    }
}

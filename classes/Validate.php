<?php

class Validate
{
    //Если прошла проверка $passed = true;
    private $passed = false, $errors = [], $db = null;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    //$source - источник информации (то что нужно нам проверить), $items - массив с правилами
    public function check($source, $items = [])
    {
        /*
        $items = весь массив
        $item = один элемент
        $rules = ключи
        $rule => $rule_value = ключ => значание

        */

        //Добираемся до ключей
        foreach ($items as $item => $rules) {
            foreach ($rules as $rule => $rule_value) {
                /*
                 * $source - POST
                 * $item - ключи в массиве 'username', 'password' ...
                 *
                 * */
                $value = $source[$item];

                //Если не был введен username
                if ($rule == 'required' && empty($value)) {
                    $this->addError("{$item} is required");
                } else if (!empty($value)) {
                    /* Перебираем кейсами ключи $rule ихние значения - min, max ...*/
                    switch ($rule) {
                        //Если длина полученой строки ($_POST) меньше заданой в массиве $rule_value
                        case 'min':
                            if (strlen($value) < $rule_value) {
                                $this->addError("{$item} must be a minimum of {$rule_value} characters");
                            }
                        break;


                        case 'max':
                            //Если длина строки больше заданой
                            if (strlen($value) > $rule_value) {
                                $this->addError("{$item} must be a maximum of {$rule_value} characters");
                            }
                        break;


                        case 'matches':
                            //Если не совпадают два поля - выводим ошибку
                            if ($value != $source[$rule_value]) {
                                $this->addError("{$rule_value} must match {$item}");
                            }
                        break;

                        case 'unique':
                            //get('users', username = value)
                            $check = $this->db->get($rule_value, [$item, '=', $value]);
                            if ($check->count()) {
                                $this->addError("{$item} already exists.");
                            }
                        break;

                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                $this->addError("{$item} is not an email");
                            }
                        break;
                    }
                }
            }
        }
        if (empty($this->errors)) {
            $this->passed = true;
        }

        return $this;

    }

    //Записывает в свойство $errors ошибки: addError(сообщение с ошибкой)
    public function addError($error)
    {
        $this->errors[] = $error;
    }


    //Возвращает $errors (массив с ошибками)
    public function errors()
    {
        return $this->errors;
    }


    //Возвращает $passed
    public function passed()
    {
        return $this->passed;
    }
}

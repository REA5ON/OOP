<?php
/*
(get, delete) -> (action) -> (query) -> ($instance)
(insert) ------------------>
*/
class Database {
    //static вызываеться при обращении к классу не к обьекту
    private static $instance = null;
    private $pdo, $query, $error = false, $results, $count;


    //метод __construct() вызываеться при создании экземпляра
    private function __construct() {
        try {
//            $this->pdo = new PDO('mysql:host=localhost;dbname=test', 'root', 'root');
            $this->pdo = new PDO('mysql:host=' . Config::get('mysql.host') . ';dbname=' . Config::get('mysql.database') .'', Config::get('mysql.username'), Config::get('mysql.password'));
        } catch (PDOException $exception) {
            die($exception->getMessage());
        }
    }


    /*При обращении к getInstance() мы проверяем пустое ли свойство $instance,
    Если да то записываем в него наш весь экземпляр
    А как мы помним что при создании экземпляра вызываеться в первую очередь метод __construct()*/

    public static function getInstance() {
        //синглтон - если не существует то создаем
        if (!isset(self::$instance)) {
            //self:: - ссылка на статичные методы или свойства класса
            self::$instance = new Database();
        }

        return self::$instance;

    }


    //query - обертка над самим PDO (Выполняем запросы напрямую)
    public function query($sql, $params = []) {
        //по умолчанию false
        $this->error = false;
        //подготовили запрос
        $this->query = $this->pdo->prepare($sql);

        //если не пустой $param
        if (count($params)) {
            //индекс(?,?,?,?) и значение[0,1,2,3]
            //привязываем индексы к переданным параметрам
            $i = 1;
            foreach ($params as $param) {
                //bindValue(индекс, значение)
                $this->query->bindValue($i, $param);
                $i++;
            }

        }

        //если execute вернет ошибку то записываем ее в error
        if (!$this->query->execute()) {
            $this->error = true;
        } else {
            //иначе получаем все результаты, записываем в results
            $this->results = $this->query->fetchAll(PDO::FETCH_OBJ);
            $this->count = $this->query->rowCount();
        }


        return $this;
    }

    //публичным методом добираемся к переменной error
    public function error() {
        return $this->error;
    }


    //публичным методом добираемся к переменной results
    public function results() {
        return $this->results;
    }


    //публичным методом добираемся к переменной count
    public function count() {
        return $this->count;
    }


    //принимаем название таблицы и массив
    public function get($table, $where = []) {
        return $this->action('SELECT *', $table, $where);
    }

    public function delete($table, $where = []) {
        return $this->action('DELETE', $table, $where);
    }


    //Обертка над query
    public function action($action, $table, $where = []) {
        if (count($where) === 3) {
            $operators = ['=', '>', '<', '>=', '<='];

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            //если переданый аргумент присутствует в массиве
            if (in_array($operator, $operators)) {
                //всё что делали - это принимали данные и готовили эту строчку для отправки её в query
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
                if (!$this->query($sql, [$value])->error()) {//если переданые SQL и массив не содержат ошибок
                    return $this;//возвращаем текуший обьект
                }
            }
        }

        return false;
    }

    //подготавливаем запрос и передаем в query
    public function insert($table, $fields = []) {
        $values = '';
        foreach ($fields as $field) {
            //сколько элементов в $field - столько и записываем "?," в $values;
            $values .= "?,";
        }
        //убираем справа запятую
        $values = rtrim($values, ',');

        //склеиваем запрос
        $sql = "INSERT INTO {$table} (" . '`' . implode('`, `', array_keys($fields)) . '`' . ") VALUES ({$values})";

        //если без ошибок - возвращаем true;
        if (!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    public function update($table, $id, $fields =[]) {
        $set = '';
        //меняем ключ на ?
        foreach ($fields as $key => $field) {
            $set .= "{$key} = ?,"; //username = ?, password = ?
        }

        $set = rtrim($set, ',');

        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

        //если без ошибок - возвращаем true;
        if (!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }


    public function first() {
        return $this->results()[0];
    }
}







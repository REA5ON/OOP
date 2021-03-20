<?php

require_once '../init.php';

$user = new User();
if ($user->hasPermissions('admin')) {
    if (Input::exists('get')) {
        $delete_id = Input::get('id');

        Database::getInstance()->delete('users', ['id', '=', $delete_id]);
        Session::flash('success', 'Пользователь удалён');
        Redirect::to('index.php');
    }
}

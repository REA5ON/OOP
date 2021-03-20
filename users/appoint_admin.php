<?php
require_once '../init.php';

$user = new User();
if ($user->hasPermissions('admin')) {
    if (Input::exists('get')) {
        $edit_user = new User(Input::get('id'));

        if ($edit_user->data()->group_id == 1) {
            Database::getInstance()->update('users', $edit_user->data()->id, ['group_id' => 2]);
        } elseif ($edit_user->data()->group_id == 2) {
            Database::getInstance()->update('users', $edit_user->data()->id, ['group_id' => 1]);
        }
        Session::flash('success', 'Данные успешно обновлены!');
        Redirect::to('index.php');
    }
}


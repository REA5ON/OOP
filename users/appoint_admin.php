<?php
require_once '../init.php';

$id = Input::get('id');
$user = new User();

if ($user->hasPermissions('admin')) {
    if (Input::exists('get')) {
        $edit_user = new User($id);

        if ($edit_user->data()->group_id == 1) {
            $user->update(['group_id' => 2], $id);
        } elseif ($edit_user->data()->group_id == 2) {
            $user->update(['group_id' => 1], $id);
        }

        Session::flash('success', 'Данные успешно обновлены!');
        Redirect::to('index.php');
    }
}


<?php
require_once 'init.php';

echo Session::flash('success');
$user = new User();
$validate = new Validate();

if ($user->isLoggedIn()) {

    if (Input::exists()) {

        if (Token::check(Input::get('token'))) {
            $validate->check($_POST, [
                'username' => ['required' => true, 'min' => 3],
                'status' => ['required' => true]
            ]);

            if ($validate->passed()) {
                $user->update([
                    'username' => Input::get('username'),
                    'status' => Input::get('status')]);
                Session::flash('success', 'Профиль успешно обновлен!');
                Redirect::to('profile.php');
            }
        }
    }
} else {
    Redirect::to('index.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">User Management</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Главная</a>
            </li>
            <?php if ($user->hasPermissions('admin')) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="users/index.php">Управление пользователями</a>
                </li>
            <?php endif; ?>
        </ul>

        <ul class="navbar-nav">
            <li class="nav-item">
            <li class="nav-item">
                <a href="profile.php" class="nav-link">Профиль</a>
            </li>
            <a href="logout.php" class="nav-link">Выйти</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Профиль пользователя - <?php echo $user->data()->username ?></h1>
<!--            --><?php //if (Session::exists('success')) : ?>
<!--                <div class="alert alert-success">-->
<!--                    --><?php //echo Session::flash('success') ?>
<!--                </div>-->
<!--            --><?php //endif; ?>
            <?php $errors = $validate->errors();
            if(!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) : ?>
                        <ul>
                            <li><?php echo $error?></li>
                        </ul>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <ul>
                <li><a href="changepassword.php?id=<?php echo $user->data()->id?>">Изменить пароль</a></li>
            </ul>
            <form action="" class="form" method="post">
                <div class="form-group">
                    <label for="username">Имя</label>
                    <input type="text" name="username" id="username" class="form-control"
                           value="<?php echo $user->data()->username ?>">
                </div>
                <div class="form-group">
                    <label for="status">Статус</label>
                    <input type="text" id="status" name="status" class="form-control"
                           value="<?php echo $user->data()->status ?>">
                </div>
                <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

                <div class="form-group">
                    <button class="btn btn-warning">Обновить</button>
                </div>
            </form>


        </div>
    </div>
</div>
</body>
</html>
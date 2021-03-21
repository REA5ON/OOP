<?php
require_once '../init.php';

$user = new User();
$validate = new Validate();

$edit_user = new User(Input::get('id'));

if ($user->isLoggedIn() && $user->hasPermissions('admin')) {

    if (Input::exists()) {

        if (Token::check(Input::get('token'))) {
            $validate->check($_POST, [
                'username' => ['required' => true, 'min' => 3],
                'status' => ['required' => true]
            ]);

            if ($validate->passed()) {

                $user->update([
                    'username' => Input::get('username'),
                    'status' => Input::get('status')], Input::get('id'));
                Session::flash('success', 'Профиль успешно обновлен!');
                Redirect::to('index.php');
            }
        }
    }
} else {
    Redirect::to('../index.php');
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
                <a class="nav-link" href="../index.php">Главная</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Управление пользователями</a>
            </li>
        </ul>

        <ul class="navbar-nav">
            <li class="nav-item">
            <li class="nav-item">
                <a href="../profile.php?id=<?php echo $user->data()->id?>" class="nav-link">Профиль</a>
            </li>
            <a href="../logout.php" class="nav-link">Выйти</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h1>Профиль пользователя - <?php echo $edit_user->data()->username?></h1>
            <?php if (Session::exists('success')) : ?>
                <div class="alert alert-success">
                    <?php echo Session::flash('success') ?>
                </div>
            <?php endif; ?>

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
            <form action="" method="post" class="form">
                <div class="form-group">
                    <label for="username">Имя</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo $edit_user->data()->username?>">
                </div>
                <div class="form-group">
                    <label for="status">Статус</label>
                    <input type="text" id="status" name="status" class="form-control" value="<?php echo $edit_user->data()->status?>">
                </div>
                <input type="hidden" name="token" value="<?php echo Token::generate() ?>">

                <div class="form-group">
                    <button class="btn btn-warning">Обновить</button>
                </div>
            </form>


        </div>
    </div>
</div>
</body>
</html>
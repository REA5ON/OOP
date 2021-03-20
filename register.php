<?php
require_once 'init.php';

$validate = new Validate();
$user = new User;
//Выполняем всю проверку
if (Input::exists()) { //Была ли отправлена форма. Если да то начинаем функцию валидации


    $validation = $validate->check($_POST, [
        'username' => [
            'required' => true,
            'min' => 2,
            'max' => 15,
        ],
        'email' => [
            'required' => true,
            'email' => true,
            'unique' => 'users'
        ],
        'password' => [
            'required' => true,
            'min' => 3
        ],
        'password_again' => [
            'required' => true,
            'matches' => 'password'
        ]
    ]);

    //Если проверка выполнена - выводим 'passed'
    if ($validation->passed()) {


        $user->create([
            'date_registered' => date("d/m/Y"),
            'email' => Input::get('email'),
            'username' => Input::get('username'),
            'password' => password_hash(Input::get('password'), PASSWORD_DEFAULT),
            'status' => ''
        ]);

        Session::flash('success', 'Регистрация успешна!');
        Redirect::to("login.php");
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="text-center">
<form class="form-signin" action="" method="post">
    <img class="mb-4" src="images/apple-touch-icon.png" alt="" width="72" height="72">
    <h1 class="h3 mb-3 font-weight-normal">Регистрация</h1>

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

    <div class="form-group">
        <input type="email" class="form-control" name="email" id="email" placeholder="Email"
               value="<?php echo Input::get('email') ?>">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" id="username" name="username" placeholder="Ваше имя"
               value="<?php echo Input::get('username') ?>">
    </div>
    <div class="form-group">
        <input type="password" class="form-control" id="password" name="password" placeholder="Пароль">
    </div>

    <div class="form-group">
        <input type="password" class="form-control" id="password_again" name="password_again"
               placeholder="Повторите пароль">
    </div>

    <div class="checkbox mb-3">
        <label>
            <input type="checkbox"> Согласен со всеми правилами
        </label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Зарегистрироваться</button>
    <p class="mt-5 mb-3 text-muted">&copy; 2017-2020</p>
</form>
</body>
</html>

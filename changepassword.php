<?php

require_once 'init.php';

$user = new User;
$validate = new Validate();

if ($user->isLoggedIn()) {
    if (Input::exists()) {
        if (Token::check(Input::get('token'))) {

            $validate->check($_POST, [
                'current_password' => ['required'=>true, 'min'=>6],
                'new_password' => ['required'=>true, 'min'=>6],
                'new_password_again' => ['required'=>true, 'min'=>6, 'matches'=>'new_password']
            ]);

            if ($validate->passed()) {
                if (password_verify(Input::get('current_password'), $user->data()->password)) {
                    $user->update(['password' => password_hash(Input::get('new_password'), PASSWORD_DEFAULT)]);
                    Session::flash('success', 'Пароль обновлен!');
                    Redirect::to('changepassword.php');
                } else {
                    $validate->addError('Invalid current password');
                }
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
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
         <h1>Изменить пароль</h1>
           <?php if (Session::exists('success')) : ?>
               <div class="alert alert-success">
                   <?php echo Session::flash('success'); ?>
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
         <ul>
           <li><a href="profile.php">Изменить профиль</a></li>
         </ul>
         <form action="" class="form" method="post">
           <div class="form-group">
             <label for="password">Текущий пароль</label>
             <input type="password" name="current_password" id="password" class="form-control">
           </div>
           <div class="form-group">
             <label for="new_password">Новый пароль</label>
             <input type="password" name="new_password" id="current_password" class="form-control">
           </div>
           <div class="form-group">
             <label for="current_password">Повторите новый пароль</label>
             <input type="password" name="new_password_again" id="current_password" class="form-control">
           </div>
             <input type="hidden" name="token" value="<?php echo Token::generate();?>">

           <div class="form-group">
             <button type="submit" class="btn btn-warning">Изменить</button>
           </div>
         </form>


       </div>
     </div>
   </div>
</body>
</html>
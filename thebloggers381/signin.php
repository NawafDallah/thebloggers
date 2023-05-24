<?php
ob_start();
session_start();
require_once 'db.php';
if(isset($_SESSION['user']))
{
    header("location: summary.php");
    die(); exit();
}

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $sql->prepare("select * from users where email = ? AND role='user'");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch();
    $total = $stmt->rowCount();
    if($total>0){
        $db_email = $row['email'];
        $db_password = $row['password'];
        if($email == $db_email && password_verify($password, $db_password)){
            $_SESSION['user'] = $row['id'];
            header("location: index.php");
        }else {
            $msg = "<div class='alert alert-danger'>Email or Password is incorrect.</div>";
        }
    }else {
        $msg = "<div class='alert alert-danger'>Email or Password is incorrect.</div>";
    }
}

if(isset($_SESSION['message'])){
    $msg = $_SESSION['message'];
    unset($_SESSION['message']);
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign In</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <h3 class="mt-5 mb-3">User Login</h3>
                <p>Please login to your account</p>
                <div class="card mb-3 shadow">
                    <div class="card-body">
                        <?php if(isset($msg)){ echo $msg; } ?>
                        <form method="post" class="">
                            <div class="mb-3">
                                <label for="">Email</label>
                                <input required type="email" class="form-control" name="email" placeholder="Enter Email Address">
                            </div>

                            <div class="mb-3">
                                <label for="">Password</label>
                                <input required type="password" class="form-control mb-2" name="password" placeholder="Enter Password">
                            </div>
                            <div class="">
                                <button name="submit" class="btn btn-success btn-block">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
				<center>
				    <a href="signup.php">Dont have an account?</a>
				</center>
            </div>
        </div>
    </div>
</body>
</html>
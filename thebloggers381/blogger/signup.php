<?php
ob_start();
session_start();
require_once '../db.php';
if(isset($_SESSION['blogger']))
{
    header("location: index.php");
    die(); exit();
}

if(isset($_POST['submit'])){
    $stmt = $sql->prepare("select * from users where email=?");
    $stmt->bindParam(1, $_POST['email'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();
    if($stmt->rowCount()>0){
        $msg = "<div class='alert alert-danger'>Sorry, an account with this email address already exists.</div>";
    }else{
        $password = $_POST['password'];
        $created_at = date('Y-m-d');
        $options = [ 'cost' => 11];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);
        $query = "INSERT into users (name, email, password, created_at, role) VALUES (?, ?, ?, ?, 'blogger')";
        $stmt = $sql->prepare($query);
        $stmt->bindParam(1, $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(3, $password, PDO::PARAM_STR);
        $stmt->bindParam(4, $created_at, PDO::PARAM_STR);
        $stmt->execute();
        $_SESSION['message'] = "<div class='alert alert-success'>Account created successfully.</div>";
        header("location:signin.php");
        die();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign Up</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <h3 class="mt-5 mb-3">Register Blogger Account</h3>
                <p>Enter all required fields</p>
                <div class="card mb-3 shadow">
                    <div class="card-body">
                        <?php if(isset($msg)){ echo $msg; } ?>
                        <form method="post" class="">
                           <div class="mb-3">
                                <label for="">Name*</label>
                                <input required type="text" class="form-control" name="name" placeholder="Enter Name">
                            </div>
                            <div class="mb-3">
                                <label for="">Email*</label>
                                <input required type="email" class="form-control" name="email" placeholder="Enter Email">
                            </div>
                            <div class="mb-3">
                                <label for="">Password*</label>
                                <input id="password" required type="password" class="form-control mb-2" name="password" placeholder="Enter Password">
                            </div>
                            <div class="">
                                <button name="submit" class="btn btn-success btn-block">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
				<center>
				    <a href="signin.php">Already have an account?</a>
				</center>
            </div>
        </div>
    </div>    
</body>

</html>
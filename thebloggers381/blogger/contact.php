<?php
session_start();
include '../db.php';
if(!isset($_SESSION['admin'])){
    header("location:login.php");
    die();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Contact Us Messages</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mt-5 mb-5">
            <div class="col-md-12">
                <h1 class="mb-3">Contact Messages</h1>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                    <?php
                        $stmt = $sql->prepare("select * from contact_messages order by id desc");
                        $stmt->bindParam(1, $userid, PDO::PARAM_STR);
                        $stmt->execute();
                        $messages = $stmt->fetchAll();
                        foreach($messages as $message){
                    ?>
                    <tr>
                        <td width="20%">
                            <?php echo $message['name'] ?>
                        </td>
                        <td width="25%"><?php echo $message['email'] ?></td>
                        <td width="40%"><?php echo $message['message'] ?></td>
                        <td>
                            <?php echo $message['date']; ?>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script>
        $("#reserveBook").click(function(){
            $("#reserveModal").modal("show");
        });
    </script>
</body>
</html>
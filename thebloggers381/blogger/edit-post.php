<?php
ob_start();
session_start();
require_once '../db.php';
if(!isset($_SESSION['blogger'])){
    header("location:signin.php");
    die();
}


if(isset($_GET['id'])){
    $postid = $_GET['id'];
    $stmt = $sql->prepare("select * from blog_posts where id=?");
    $stmt->bindParam(1, $postid, PDO::PARAM_STR);
    $stmt->execute();
    $post = $stmt->fetch();
    if($stmt->rowCount()==0){
        header("location:index.php");
        die();
    }
}else{
    header("location:index.php");
    die();
}

if(isset($_POST['submit'])){
    $query = "Update blog_posts set title=?, description=?, category_id=? where id=?";
    $stmt = $sql->prepare($query);
    $stmt->bindParam(1, $_POST['title'], PDO::PARAM_STR);
    $stmt->bindParam(2, $_POST['description'], PDO::PARAM_STR);
    $stmt->bindParam(3, $_POST['category'], PDO::PARAM_STR);
    $stmt->bindParam(4, $postid, PDO::PARAM_STR);
    $stmt->execute();
    $msg = "<div class='alert alert-success'>Post details updated successfully.</div>";
    $stmt = $sql->prepare("select * from blog_posts where id=?");
    $stmt->bindParam(1, $postid, PDO::PARAM_STR);
    $stmt->execute();
    $post = $stmt->fetch();
}

if(isset($_POST['update_image'])){
    unlink('../images/'.$post['image']);
    $tmpFilePath = $_FILES['image']['tmp_name'];
    if(!empty($_FILES['image']['name'])){
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid(time()).'.'.$ext;
        $newFilePath = "../images/".$image;
        move_uploaded_file($tmpFilePath, $newFilePath);
        $query = "UPDATE blog_posts set image=? where id=?";
        $stmt = $sql->prepare($query);
        $stmt->bindParam(1, $image, PDO::PARAM_STR);
        $stmt->bindParam(2, $postid, PDO::PARAM_STR);
        $stmt->execute();
        $msg = "<div class='alert alert-success'>Post image updated successfully.</div>";
        $post['image'] = $image;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Edit Post</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <style>
    
        video{
            margin-top: 35px;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-9">
                <h3 class="mt-5 mb-3">Edit Post</h3>
                <p>Enter all required fields</p>
                <div class="card mb-3 shadow">
                    <div class="card-body">
                        <?php if(isset($msg)){ echo $msg; } ?>
                        <form method="post">
                           <div class="mb-3">
                                <label for="">Title*</label>
                                <input required type="text" class="form-control" name="title" value="<?php echo $post['title']; ?>">
                            </div>
                            
                            
                            
                            <div class="mb-3">
                                <label for="">Category*</label>
                                <select required name="category" class="form-control" id="">
                                    <option value="">Select</option>
                                    <?php
                                        $stmt = $sql->prepare("select * from categories order by name asc");
                                        $stmt->execute();
                                        $categories = $stmt->fetchAll();
                                        foreach($categories as $category){
                                    ?>
                                    <option <?php if($category['id']==$post['category_id']){ echo 'selected'; } ?> value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="">Description*</label>
                                <textarea required name="description" class="form-control" id="summernote" cols="30" rows="5"><?php echo $post['description']; ?></textarea>
                            </div>
                            
                            <div class="">
                                <button name="submit" class="btn btn-success btn-block">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
			</div>
            <div class="col-md-3">
                <?php 
                    if(!empty($post['image'])){ 
                        $ext = pathinfo($post['image'], PATHINFO_EXTENSION);
                        if($ext=='mp4'){
                ?>
                <video controls src="../images/<?php echo $post['image']; ?>"></video>
                <?php }else{ ?>
                <img class="img-fluid mt-5" src="../images/<?php echo $post['image']; ?>" alt="">
                <?php }} ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3 mt-3">
                        <label for="">Update Image/Video*</label>
                        <input required type="file" name="image">
                    </div>
                    <button class="btn btn-success btn-sm" name="update_image">Update</button>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
          $('#summernote').summernote({ height:'250px' });
        });
    </script>
</body>
</html>
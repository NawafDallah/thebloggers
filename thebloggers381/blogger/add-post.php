<?php
ob_start();
session_start();
require_once '../db.php';
if(!isset($_SESSION['blogger']))
{
    header("location: signin.php");
    die(); exit();
}

if(isset($_POST['submit'])){
    $image = "";
    $tmpFilePath = $_FILES['image']['tmp_name'];
    if(!empty($_FILES['image']['name'])){
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid(time()).'.'.$ext;
        $newFilePath = "../images/".$image;
        move_uploaded_file($tmpFilePath, $newFilePath);
    }
    $created_at = date('Y-m-d');
    $query = "INSERT into blog_posts (title, author_id, image, description, created_at, category_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $sql->prepare($query);
    $stmt->bindParam(1, $_POST['title'], PDO::PARAM_STR);
    $stmt->bindParam(2, $_SESSION['blogger'], PDO::PARAM_STR);
    $stmt->bindParam(3, $image, PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST['description'], PDO::PARAM_STR);
    $stmt->bindParam(5, $created_at, PDO::PARAM_STR);
    $stmt->bindParam(6, $_POST['category'], PDO::PARAM_STR);
    $stmt->execute();
    $msg = "<div class='alert alert-success'>Post added successfully.</div>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add New Post</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-10">
                <h3 class="mt-5 mb-3">Add New Post</h3>
                <p>Enter all required fields</p>
                <div class="card mb-3 shadow">
                    <div class="card-body">
                        <?php if(isset($msg)){ echo $msg; } ?>
                        <form method="post" class="" enctype="multipart/form-data">
                           <div class="mb-3">
                                <label for="">Title*</label>
                                <input required type="text" class="form-control" name="title">
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
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Post Image OR Video</label>
                                <input type="file" class="ml-2" name="image">
                            </div>
                            <div class="mb-3">
                                <label for="">Post Content*</label>
                                <textarea required name="description" class="form-control" id="summernote" cols="30" rows="5"></textarea>
                            </div>
                            
                            <div class="">
                                <button name="submit" class="btn btn-success btn-block">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
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
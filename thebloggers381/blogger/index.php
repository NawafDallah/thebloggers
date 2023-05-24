<?php
ob_start();
session_start();
require_once '../db.php';
if(!isset($_SESSION['blogger'])){
    header("location:signin.php");
    die();
}

if(isset($_POST['delete'])){
    $query = "DELETE from blog_posts where id=?";
    $stmt = $sql->prepare($query);
    $stmt->bindParam(1, $_POST['post_id'], PDO::PARAM_STR);
    $stmt->execute();
    
    $query = "DELETE from comments where post_id=?";
    $stmt = $sql->prepare($query);
    $stmt->bindParam(1, $_POST['post_id'], PDO::PARAM_STR);
    $stmt->execute();
    
    $query = "DELETE from ratings where post_id=?";
    $stmt = $sql->prepare($query);
    $stmt->bindParam(1, $_POST['post_id'], PDO::PARAM_STR);
    $stmt->execute();
    
    $msg = "<div class='alert alert-success'>Post removed successfully.</div>";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>All Posts</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <style>
        img:hover {
            opacity: 0.7;
            -webkit-transition: all 0.35s ease;
            transition: all 0.35s ease;
        }

        img {
            width: 100%;
            height: 220px !important;
            object-fit: cover;
        }
        
        video {
            width: 100%;
            height: 220px !important;
            object-fit: cover;
        }

        a:hover {
            text-decoration: none;
            color: red;
            -webkit-transition: all 0.25s ease;
            transition: all 0.25s ease;
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">

        <div class="row justify-content-center mb-5">
            <div class="col-md-12">
                <h1 class="mt-5 mb-3">All Posts
                    <span class="float-right">
                        <a href="add-post.php" class="btn btn-sm btn-primary">Add New Post</a>
                    </span>
                </h1>
                <?php if(isset($msg)){ echo $msg; } ?>
                <div class="row">
                    <?php 
                        $stmt = $sql->prepare("select a.*, b.name as author from blog_posts as a left join users as b on a.author_id=b.id order by a.id desc");
                        $stmt->execute();
                        $posts = $stmt->fetchAll();
                        foreach($posts as $post){
                    ?>
                    <div class="col-md-4">
                        <a target="_blank" href="../post.php?id=<?php echo $post['id']; ?>">
                            <div class="card mb-3 shadow">
                                <?php 
                                    if(!empty($post['image'])){ 
                                        $ext = pathinfo($post['image'], PATHINFO_EXTENSION);
                                        if($ext=='mp4'){
                                ?>
                                <video controls src="../images/<?php echo $post['image']; ?>"></video>
                                <?php }else{ ?>
								<img src="../images/<?php echo $post['image']; ?>" alt="">
								<?php }} ?>
                                <div class="p-3">
                                    <?php 
                                        $stmt = $sql->prepare("select COUNT(*) as comments from comments where post_id=?");
                                        $stmt->bindParam(1, $post['id'], PDO::PARAM_STR);
                                        $stmt->execute();
                                        $row = $stmt->fetch();
                                        $comments = $row['comments'];
                                        
                                        $stmt = $sql->prepare("select AVG(rating) as avg_rating from ratings where post_id=?");
                                        $stmt->bindParam(1, $post['id'], PDO::PARAM_STR);
                                        $stmt->execute();
                                        $row = $stmt->fetch();
                                        $ratings = $row['avg_rating'];
                            
                                    ?>  
                                    <h3 class="mb-3"><?php echo $post['title']; ?></h3>
                                    <p>Views: <?php echo $post['views']; ?> <br> 
                                    Comments: <?php echo $comments; ?> <br> 
                                    Ratings: <?php echo round($ratings, 2); ?> <br> 
                                    </p>
                                    <form onsubmit="return confirm('Are you sure want to delete this post?')" action="" method="post">
                                        <a class="btn btn-sm btn-primary" href="comments.php?id=<?php echo $post['id']; ?>">Comments</a>
                                        <?php if($post['author_id']==$_SESSION['blogger']){ ?>
                                        <a class="btn btn-sm btn-success" href="edit-post.php?id=<?php echo $post['id']; ?>">Edit</a>
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
										<button class="btn btn-sm btn-danger" name="delete">Delete</button>
										<?php } ?>
                                    </form>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
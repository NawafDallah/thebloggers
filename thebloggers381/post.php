<?php
session_start();
include 'db.php';
if(isset($_SESSION['user'])){
    $userid = $_SESSION['user'];
}

if(isset($_GET['id'])){
    $postid = $_GET['id'];
    $stmt = $sql->prepare("select a.*, b.name as author, c.name as category from blog_posts as a left join users as b on a.author_id=b.id left join categories as c on a.category_id=c.id where a.id=? order by a.id desc");
    $stmt->bindParam(1, $postid, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()>0){
        $post = $stmt->fetch();
    }else{
        header("location:index.php");
    }
}else{
    header("location:index.php");
}

if(isset($_POST['submit_comment'])){
    $created_at = date('Y-m-d H:i');
    $query = "INSERT into comments (post_id, user_id, created_at, comment) VALUES (?, ?, ?, ?)";
    $stmt = $sql->prepare($query);
    $stmt->bindParam(1, $postid, PDO::PARAM_STR);
    $stmt->bindParam(2, $userid, PDO::PARAM_STR);
    $stmt->bindParam(3, $created_at, PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST['comment'], PDO::PARAM_STR);
    $stmt->execute();
    $msg = "<div class='alert alert-success'>Comment added successfully.</div>";
}

if(isset($_POST['submit_rating'])){
    $stmt = $sql->prepare("select * from ratings where post_id=? AND user_id=?");
    $stmt->bindParam(1, $post['id'], PDO::PARAM_STR);
    $stmt->bindParam(2, $userid, PDO::PARAM_STR);
    $stmt->execute();
    if($stmt->rowCount()>0){
        $msg = "<div class='alert alert-danger'>You have already posted your rating.</div>";
    }else{
        $query = "INSERT into ratings (post_id, user_id, rating) VALUES (?, ?, ?)";
        $stmt = $sql->prepare($query);
        $stmt->bindParam(1, $postid, PDO::PARAM_STR);
        $stmt->bindParam(2, $userid, PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST['rating'], PDO::PARAM_STR);
        $stmt->execute();
        $msg = "<div class='alert alert-success'>Rating added successfully.</div>";
    }
}

$stmt = $sql->prepare("select AVG(rating) as avg_rating from ratings where post_id=?");
$stmt->bindParam(1, $post['id'], PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch();
$ratings = $row['avg_rating'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $post['title']; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        
        .comment{
            font-size: 18px;
        }
        
        img{
            height: 350px;
            width: 100%;
            object-fit: cover;
        }
        
        video{
            margin-bottom: 15px;
            width: 100%;
            object-fit: cover;
        }
        
        .details{
            font-weight: 600;
            color: green;
        }
    </style>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mt-5">
            
            <div class="col-md-10 text-center">
                <?php if(isset($msg)){ echo $msg; } ?>
				<?php 
                    if(!empty($post['image'])){ 
                        $ext = pathinfo($post['image'], PATHINFO_EXTENSION);
                        if($ext=='mp4'){
                ?>
                <video controls src="images/<?php echo $post['image']; ?>"></video>
                <?php }else{ ?>
                <img class="mb-3" src="images/<?php echo $post['image']; ?>" alt="">
                <?php }} ?>
            </div>
            <div class="col-md-10">
                
                <h1 class="mb-3"><?php echo $post['title']; ?></h1>
                <p>
                    <span class="details">Author:</span> <?php echo $post['author']; ?> | <span class="details">Added on:</span> <?php echo $post['created_at']; ?> | <span class="details">Category:</span> <?php echo $post['category']; ?>
                </p>
                <hr>
                <div class="mb-5" style="white-space:pre-line"><?php echo $post['description']; ?></div>
                <hr>
                <h1 class="mb-3">Ratings  (<?php echo round($ratings, 2); ?>)</h1>
                <?php if(isset($_SESSION['user'])){ ?>
                <form action="" method="post">
                    <div class="form-group">
                        <label>Rate this post</label>
                        <select required name="rating" class="form-control" id="">
                            <option value="">Select Rating</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="submit_rating">Submit</button>
                </form>
                <?php }else{ ?>
                <a href="signin.php" class="btn btn-primary">Please login to submit rating</a>
                <?php } ?>
                <hr>
                
                <ul class="list-group mb-5">
                <?php 
                    $stmt = $sql->prepare("select a.*, b.name as username from comments as a left join users as b on a.user_id=b.id where post_id=? order by a.id desc");
                    $stmt->bindParam(1, $post['id'], PDO::PARAM_STR);
                    $stmt->execute();
                    $comments = $stmt->fetchAll();
                    foreach($comments as $comment){
                ?>
                  <li class="list-group-item mt-3">
                      <span><?php echo $comment['username']; ?></span>
                      <p class="mb-0 comment"><?php echo $comment['comment']; ?></p>
                      <small><?php echo $comment['created_at']; ?></small>
                  </li>
                  
                  <?php 
                    $stmt = $sql->prepare("select a.*, b.name as username from reply_comments as a left join users as b on a.author_id=b.id where comment_id=? order by a.id desc");
                    $stmt->bindParam(1, $comment['id'], PDO::PARAM_STR);
                    $stmt->execute();
                    $replies = $stmt->fetchAll();
                    foreach($replies as $reply){
                ?>
                  <div class="pl-5">
                      <li class="list-group-item mt-2">
                          <span><?php echo $reply['username']; ?></span>
                          <p class="mb-0 comment"><?php echo $reply['reply']; ?></p>
                          <small><?php echo $reply['created_at']; ?></small>
                      </li>
                  </div>
                <?php } ?>
                  
                  
                <?php } ?>
                </ul>
                
                <?php if(isset($_SESSION['user'])){ ?>
                <form action="" method="post" class="mb-5">
                   <div class="form-group">
                       <label for="">Enter Comment</label>
                       <textarea required name="comment" class="form-control" id="" cols="30" rows="5"></textarea>
                   </div>
                   <div class="form-group">
                       <button class="btn btn-primary" name="submit_comment">Submit</button>
                   </div>
                </form>
                <?php }else{ ?>
                <div class="mb-5">
                    <a class="btn btn-danger" href="signin.php">Please sign in to post comments</a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
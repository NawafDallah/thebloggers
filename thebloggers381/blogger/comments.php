<?php
session_start();
include '../db.php';
if(!isset($_SESSION['blogger'])){
    header("location:signin.php");
    die();
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

if(isset($_POST['submit_reply'])){
    $created_at = date('Y-m-d H:i');
    $query = "INSERT into reply_comments (comment_id, author_id, created_at, reply) VALUES (?, ?, ?, ?)";
    $stmt = $sql->prepare($query);
    $stmt->bindParam(1, $_POST['comment_id'], PDO::PARAM_STR);
    $stmt->bindParam(2, $_SESSION['blogger'], PDO::PARAM_STR);
    $stmt->bindParam(3, $created_at, PDO::PARAM_STR);
    $stmt->bindParam(4, $_POST['reply'], PDO::PARAM_STR);
    $stmt->execute();
    $msg = "<div class='alert alert-success'>Comment added successfully.</div>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Comments</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        
        .comment{
            font-size: 18px;
        }
        
        img{
            height: 350px;
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
            
            
            <div class="col-md-10">
                <?php if(isset($msg)){ echo $msg; } ?>
                <h1 class="mb-3"><?php echo $post['title']; ?></h1>
                
                <hr>
                
                <h3 class="mb-3">Comments</h3>
                
                <?php 
                    $stmt = $sql->prepare("select a.*, b.name as username from comments as a left join users as b on a.user_id=b.id where post_id=? order by a.id desc");
                    $stmt->bindParam(1, $post['id'], PDO::PARAM_STR);
                    $stmt->execute();
                    $comments = $stmt->fetchAll();
                    foreach($comments as $comment){
                ?>
                  <div class="card card-body mb-5 shadow">
                      
                      <div class="card card-body">
                          <span><?php echo $comment['username']; ?></span>
                          <p class="mb-0 comment"><?php echo $comment['comment']; ?></p>
                          <small><?php echo $comment['created_at']; ?></small>
                      </div>
                      <h4 class="mt-3">Replies</h4>
                      <?php 
                            $stmt = $sql->prepare("select a.*, b.name as username from reply_comments as a left join users as b on a.author_id=b.id where comment_id=? order by a.id desc");
                            $stmt->bindParam(1, $comment['id'], PDO::PARAM_STR);
                            $stmt->execute();
                            $replies = $stmt->fetchAll();
                            foreach($replies as $reply){
                        ?>
                        <div class="pl-5">
                            <div class="card card-body">
                                <span><?php echo $reply['username']; ?></span>
                                <p class="mb-0 comment"><?php echo $reply['reply']; ?></p>
                                <small><?php echo $reply['created_at']; ?></small>
                            </div>
                        </div>
                        <?php } ?>
                      
                      
                      <form action="" method="post" class="mt-5 pl-5">
                          <div class="form-group">
                              <label for="">Enter Reply</label>
                              <textarea required name="reply" class="form-control" id="" cols="30" rows="5"></textarea>
                          </div>
                          <div class="form-group">
                              <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                              <button class="btn btn-primary" name="submit_reply">Reply</button>
                          </div>
                      </form>
                  </div>
                <?php } ?>
                
                
                
                
            </div>
        </div>
    </div>
    <div class="modal fade" id="reserveModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reserve Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="">Select days to return the book</label>
                            <select required name="return_days" class="form-control" id="">
                                <option value="">Select</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php //include 'footer.php'; ?>
    <script>
        $("#reserveBook").click(function(){
            $("#reserveModal").modal("show");
        });
    </script>
</body>
</html>
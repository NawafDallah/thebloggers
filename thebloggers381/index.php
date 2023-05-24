<?php
ob_start();
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Blog</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
    <style>
        img:hover{
            opacity: 0.7;
            -webkit-transition: all 0.35s ease;
            transition: all 0.35s ease;
        }
        
        img, video{
            width: 100%;
            height: 220px !important;
            object-fit: cover;
        }
        
        a:hover{
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
        <h1 class="mt-5 mb-3">Most Popular Posts</h1>
        
        <div class="row mb-5 mt-3">
           <div class="col-md-12 mb-3">
               <form action="" method="get">
                   <div class="form-group">
                       <input required type="text" class="form-control" name="q" placeholder="Search Posts" value="<?php if(isset($_GET['q'])){ echo $_GET['q']; } ?>">
                   </div>
               </form>
           </div>
            <?php 
                if(isset($_GET['q'])){
                    $q = "%".$_GET['q']."%";
                    $stmt = $sql->prepare("select a.*, b.name as author, c.name as category from blog_posts as a left join users as b on a.author_id=b.id left join categories as c on a.category_id=c.id where title LIKE ? OR description LIKE ? order by a.id desc");
                    $stmt->bindParam(1, $q, PDO::PARAM_STR);
                    $stmt->bindParam(2, $q, PDO::PARAM_STR);
                }else{
                    $stmt = $sql->prepare("select a.*, b.name as author, c.name as category from blog_posts as a left join users as b on a.author_id=b.id left join categories as c on a.category_id=c.id order by a.id desc");
                }
                
                $stmt->execute();
                $posts = $stmt->fetchAll();
                foreach($posts as $post){
            ?>
            <div class="col-md-4">
                <div class="card mb-5">
                    <a href="post.php?id=<?php echo $post['id']; ?>">
						<?php 
                            if(!empty($post['image'])){ 
                                $ext = pathinfo($post['image'], PATHINFO_EXTENSION);
                                if($ext=='mp4'){
                        ?>
                        <video controls src="images/<?php echo $post['image']; ?>"></video>
                        <?php }else{ ?>
                        <img src="images/<?php echo $post['image']; ?>" alt="">
                        <?php }} ?>
                        <div class="p-2">
                            <h4><?php echo $post['title']; ?></h4>
                            <p class="text-muted"><?php echo $post['category']; ?> | <?php echo $post['created_at']; ?></p>
                        </div>
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>
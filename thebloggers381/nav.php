<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">TheBloggers381</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarColor02">
        <ul class="navbar-nav ml-auto">
            <?php if(!isset($_SESSION['user'])){ ?>
            <li class="nav-item"><a class="nav-link" href="signin.php">Sign In</a></li>
            <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
            <li class="nav-item"><a class="nav-link" href="blogger/">Blogger</a></li>
            <?php }else{ ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php } ?>
            
        </ul>
    </div>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">TheBloggers381</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor02" aria-controls="navbarColor02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarColor02">
        <ul class="navbar-nav ml-auto">
            <?php if(isset($_SESSION['blogger'])){ ?>
            <li class="nav-item"><a class="nav-link" href="index.php">View All Posts</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>
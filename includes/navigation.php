   
<nav class="navbar navbar-expand-lg navbar-light navbar-fixed-top" id="mainNav" style="background-color: #00BDA5; border: 0px;">
    <div class="container" style="color: white;">
      <a class="navbar-brand" href="/" >
        <img src="/images/WhiteLogo.png" style="width: 27px; float: left;">
        <h2>Niterria</h2>
      </a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" >
        Menu
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto" >
          <li class="nav-item">
            <a class="nav-link" href="/about">About</a>
          </li>
          
        <?php if(isLoggedIn()){ ?>
            <li class="nav-item">
              <a class="nav-link" href="/includes/logout.php">Logout</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/profile">Profile</a>
            </li>
            <?php if(is_admin()){ ?>
                <li class="nav-item">
                  <a class="nav-link" href="/admin">Admin</a>
                </li>
            <?php } ?>
            <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link" href="/registration">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/login">Login</a>
                </li>
          <?php } ?>
            
        </ul>
      </div>
    </div>
  </nav>

   
<nav class="navbar navbar-expand-lg navbar-light navbar-fixed-top" id="mainNav" style="background: linear-gradient(120deg,rgba(38, 14, 208, 1) 50%, rgba(83, 41, 237, 1) 100%); border-bottom: solid black 3px; ">
    <div class="container" style="color: white;">
      <a class="navbar-brand" href="<?php echo BASE_URL; ?>" >
        <img src="<?php echo BASE_URL; ?>/images/WhiteLogo.png" style="width: 35px; float: left; margin-right: 5px">
        <h1>Niterria</h1>
      </a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation" >
        Menu
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto" >
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/about">About</a>
          </li>
          
        <?php if(isLoggedIn()){ ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/includes/logout.php">Logout</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo BASE_URL; ?>/profile">Profile</a>
            </li>
            <?php if(is_admin()){ ?>
                <li class="nav-item">
                  <a class="nav-link" href="<?php echo BASE_URL; ?>/admin">Admin</a>
                </li>
            <?php } ?>
            <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/registration">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/login">Login</a>
                </li>
          <?php } ?>
            
        </ul>
      </div>
    </div>
  </nav>

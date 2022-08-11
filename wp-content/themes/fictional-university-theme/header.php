<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
      <meta charset = "<?php bloginfo("charset"); ?>">
        <meta name = "viewport" content = "width = device-width, initial-scale = 1">
          <?php wp_head(); ?> 
          <!-- WP calls styles so modify functions.php to load scripts -->
    </head>
    <body <?php body_class(); ?>>
      <header class="site-header">
        <div class="container">
          <h1 class="school-logo-text float-left">
            <a href="<?php echo esc_url(site_url());  ?>"><strong>Fictional</strong> University</a>
          </h1>
          <a href="<?php echo esc_url(site_url('/search')); ?>" class="js-search-trigger site-header__search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
          <i class="site-header__menu-trigger fa fa-bars" aria-hidden="true"></i>
          <div class="site-header__menu group">
            <!-- Navigation UL Mobile Layout-->
            <nav class="main-navigation">
              <ul>
                <li 
                  <?php if (is_page('about-us') or wp_get_post_parent_id(0) == 15) echo 'class = "current-menu-item"'?> >
                  <a href="<?php echo esc_url(site_url('/about-us')); ?>">About Us</a>
                </li> 
                <li 
                  <?php if (get_post_type() == 'program') echo 'class="current-menu-item"'?>><a href="<?php echo get_post_type_archive_link('program') ?>">Programs</a>
                </li>
                <li 
                  <?php if (get_post_type() == 'event' OR is_page("past-events")) echo 'class = "current-menu-item"' ?>><a href="<?php echo get_post_type_archive_link('event'); ?>">Events</a>
                </li>
                <li 
                  <?php if (get_post_type() == 'post') echo 'class = "current-menu-item"' ?>>
                <a href="<?php echo site_url('/blog'); ?>">Blog</a>
                </li>
              </ul>
            </nav>
            <!-- Desktop Layout -->
            <div class="site-header__util">
              <!-- set login and sign up buttons to display conditionally -->
              <?php 
                if (is_user_logged_in()) { ?>
                <!-- My Notes button -->
                  <a href="<?php echo esc_url(site_url('/my-notes')); ?>" class="btn btn--small btn--orange float-left push-right">My Notes</a>
                  
                  <!-- Log Out button -->
                  <a href="<?php echo wp_logout_url(); ?>" class="btn btn--small btn--dark-orange float-left btn--with-photo">
                  <span class="site-header__avatar"><?php echo get_avatar(get_current_user_id(), 60) ?></span>
                  <span class="btn__text">Log Out</span>
                  </a>

               <?php } else { ?>
                <!-- Login and Sign Up buttons -->
                  <a href="<?php echo wp_login_url(); ?>" class="btn btn--small btn--orange float-left push-right">Login</a>
                  <a href="<?php echo wp_registration_url(); ?>" class="btn btn--small btn--dark-orange float-left">Sign Up</a>

               <?php  }
              ?>
             <!-- Search icon in top navigation -->
              <a href="<?php echo esc_url(site_url('/search')); ?>" class="search-trigger js-search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a>
            </div>
          </div> <!-- close class="site-header__menu group" -->
        </div> <!-- close container -->
      </header>
<!-- body and html closes in footer.php -->
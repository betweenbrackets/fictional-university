<!-- modifying the form's default url to fit WP's url -->
<!-- apply the esc_url() for security because calling a url directly from the database -->
<!-- applied the esc_url() to header.php -->

<form class="search-form" method="get" action="<?php echo esc_url(site_url('/'));?>">
    <label class="headline headline--medium" for="s">Perform a New Search </label>
    <div class="search-form-row">
        <input placeholder="What are you looking for?"class="s" type="search" name = "s">
        <input class="search-submit" type="submit" value="Search">
    </div>
</form>
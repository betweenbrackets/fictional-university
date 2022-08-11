<!-- https://codex.wordpress.org/Creating_a_Search_Page -->
<!-- if your theme folder contains a file named search.php, WordPress will use that to power the result screen instead of index.php. -->
<?php 
/* 
Template Name: Search Page
*/
?>

<?php 
get_header();
pageBanner(array(
  'title' => 'Search Results',
  // by default, get_search_query() escapes JS into simple text
  // esc_html() applied, so overide g_s_q() by setting to false
  'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query(false)) . '&rdquo;.'
));
?>

<div class="container container--narrow page-section">
  <?php 
    if (have_posts()) { 
        while(have_posts()) {
            the_post(); echo the_post();
            get_template_part('template-parts/content', get_post_type());
            } // close of the loop
          echo paginate_links(); 
    } else {
        echo '<h2 class="headline headline--small-plus">No results match that search.</h2>';
    }
    get_search_form();
  ?>
</div>

<?php get_footer(); ?>
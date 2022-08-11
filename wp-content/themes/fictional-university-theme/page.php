
<!-- However, even though this screen only displays information for that one single page, we see that the

headline is still a link, which means that this you órale and this screen is being powered by index

dot BHP instead of single dot p HP.

And that's because WordPress only uses the single dot BHP file for individual posts for individual pages.

WordPress looks in our theme folder for a file named Page Dot p HP. -->

<?php get_header(); ?>

<?php
/* used for individual pages */
    while(have_posts()) {
        the_post(); 
        pageBanner();?>

        <div class="container container--narrow page-section">

        <?php 
        $theParent = wp_get_post_parent_id(get_the_ID());
           if ($theParent) { ?>
               <div class="metabox metabox--position-up metabox--with-home-link">
                    <p><a class="metabox__blog-home-link" href="<?php echo get_permalink($theParent); ?>"><i class="fa fa-home" aria-hidden="true"></i><?php echo get_the_title($theParent); ?></a> <span class="metabox__main"><?php the_title();?></span></p>
                </div>
           <?php }
        ?>

    <?php 
        $testArray = get_pages(array(
            'child_of' => get_the_ID(),
        ));

        if ($theParent or $testArray) { ?>
        <div class="page-links">
            <h2 class="page-links__title"><a href="<?php echo get_permalink($theParent) ?>"><?php echo get_the_title($theParent) ?></a></h2>
            <ul class="min-list">
            <?php 

            if ($theParent) {
                $findChildrenOf = $theParent;
            } else {
                $findChildrenOf = get_the_ID();
            }
            /* takes and associative array as an argument*/ 
                wp_list_pages(array(
                    'title_li' => NULL,
                    'child_of' => $findChildrenOf,
                    'sort_column' => 'menu_order'
                ));
            ?>
            </ul>
        </div>
        <?php } ?>
        
        <div class="generic-content">
            <p><?php the_content(); ?></p>
        </div>
        </div>

    <?php }

?>

<?php get_footer(); ?>

<!-- This page powers the page -http://localhost:8888/fictional-university/page-1/ -->
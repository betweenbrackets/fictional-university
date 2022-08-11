<!-- single.php powers the blog post structure: Blog Home and posted by Author on Date
for each custom post type then need a single-custom-post-type file -->
<?php get_header()?>

<?php
    while(have_posts()) {
        the_post(); 
        pageBanner();
        ?>
        <!-- replaced by pageBanner() -->
        <!-- <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url(<?php //echo get_theme_file_uri('images/ocean.jpg'); ?>)"></div>
                <div class="page-banner__content container container--narrow">
                    <h1 class="page-banner__title"><?php the_title(); ?></h1>
                    <div class="page-banner__intro">
                    <p>TODO: REPLACE THIS TEXT</p>
                </div>
            </div>
        </div> -->

        <div class="container container--narrow page-section">
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('event'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Events Home </a> <span class="metabox__main"><?php the_title(); ?><?php echo get_the_category_list(', '); ?></span></p>
            </div>
            <div class = "generic-content"><?php the_content(); ?></div>

            <?php 
                // creating a Related Field to connect events to program (one to many)
                // leveraging function from ACF
                // takes as an argument the field name generated in ACF
                // this field returns an array
                $relatedPrograms = get_field('related_programs');

                if ($relatedPrograms) {
                    // style the list
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium">Related Programs</h2>';
                    echo '<ul class= "link-list min-list">';
                    // $program is a new variable declaration
                    foreach($relatedPrograms as $program) { ?>
                        <?php
                        // iterate throough the array to return desired values
                        // *** we're not in a main WP loop with a main WP query so no the_title();

                        // takes as argument a post object which each item that lives in the $relatedProgram array
                        // get functions don't handle the echo return for you
                        
                        // echo get_the_title($program);
                        ?>
                        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
                <?php }
                echo '</ul>';
                }
                 
            ?>
        </div>
    <?php }
get_footer(); 
?>

<!-- This page powers the single display of a post. http://localhost:8888/fictional-university/2021/09/16/post-4/ -->
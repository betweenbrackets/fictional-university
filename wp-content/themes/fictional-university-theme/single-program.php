<!-- single.php powers the blog post structure: Blog Home and posted by Author on Date
for each custom post type then need a single-custom-post-type file -->
<?php get_header() ?>

<?php
    while(have_posts()) {
        the_post(); 
        pageBanner();
        ?>

        <div class="container container--narrow page-section">
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs </a> <span class="metabox__main"><?php the_title(); ?></span></p>
            </div>
            <div class = "generic-content"><?php the_field('main_body_content'); ?></div>
            
            <?php  
            // program custom query for the Professor CPT to look for professors
                $relatedProfessors = new WP_Query(array(
                    'posts_per_page' => -1,
                    'post_type' => 'professor',
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'meta_query' => array(
                        // inner array to filter for program
                        array(
                            // if the array of related programs (ACF function) contains the ID 
                            # of current programs post
                            'key' => 'related_programs',
                            'compare'=> 'LIKE',
                            // WordPress stores arrays as serialized arrays
                            // but WordPress wraps IDs in quotation marks
                            // so concatenate quotation marks onto function return
                            'value' => '"' . get_the_ID() . '"'
                        )
                    )
                ));

                if ($relatedProfessors->have_posts()) {
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium"> ' . get_the_title() . ' Professors </h2>';
                    echo '<ul class="professor-cards">';
                    // the while loop loops through the object to output each related event
                    while($relatedProfessors->have_posts()) {
                        $relatedProfessors->the_post(); ?>
                        <li class="professor-card__list-item">
                            <a class="professor-card" href="<?php the_permalink() ?>">
                                <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscape'); ?>">
                                <span class="professor-card__name">
                                   <?php the_title(); ?> 
                                </span>
                            </a>
                        </li>
                    <?php }
                    echo '</ul>';
                } 
                // resets the global Post Object and resets back to the default URL
                // whenever run multiple custom queries on a single page, put this in between the two queries
                wp_reset_postdata();  
            ?>
            <?php 
            // relating programs to events (one to many)
            // the problem: a program is only aware of its title and its content
            // the solution: the WP database is aware of everything
            // write a custom query
            // question: have we already written a similar custom query?
            // answer: front-page.php is already querying for upcoming events
            ?>
            <?php 
            // Program Custom Query/Post Type -- custom query that looks for related events
            // set a variable that is an object (an instance of the WP_Query class) that is equal to the result set 
            ?>
            <?php 
                $today = date('Ymd');
                $homepageEvents = new WP_Query(array(
                    'posts_per_page' => 2,
                    'post_type' => 'event',
                    'meta_key' => 'event_date',
                    'orderby' => 'meta_value_num',
                    'order' => 'ASC',
                    'meta_query' => array(
                        // think of this inner array as a filter
                        array(
                            'key' => 'event_date',
                            'compare' => '>=',
                            /* use variable and declare above */
                            'value' => $today,
                            /* set type of what comparing */
                            'type' => 'numeric'
                        ),
                        // inner array to filter for program
                        array(
                            // if the array of related programs (ACF function) contains the ID 
                            # of current programs post
                            'key' => 'related_programs',
                            'compare'=> 'LIKE',
                            // WordPress stores arrays as serialized arrays
                            // but WordPress wraps IDs in quotation marks
                            // so concatenate quotation marks onto function return
                            'value' => '"' . get_the_ID() . '"'
                        )
                    )
                ));

                if ($homepageEvents->have_posts()) {
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium"> Upcoming ' . get_the_title() . ' Events </h2>';
                    // the while loop loops through the object to output each related event
                    while($homepageEvents->have_posts()) {
                        $homepageEvents->the_post();
                        get_template_part('template-parts/content-event');
                    }
                }
            ?>
        </div>
    <?php }
get_footer(); 
?>

<!-- This page powers the single display of a post. http://localhost:8888/fictional-university/2021/09/16/post-4/ -->
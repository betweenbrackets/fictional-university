<!-- single.php powers the blog post structure: Blog Home and posted by Author on Date
for each custom post type then need a single-custom-post-type file -->
<!-- Like.js has the client-side functions -->
<!-- like-route.php has the server-side functions -->
<?php get_header() ?>

<?php
    while(have_posts()) {
        the_post(); 
        pageBanner();
        ?>
        <div class="container container--narrow page-section">
            <!-- deleted metabox  -->
            <div class = "generic-content">
                <div class="row group">
                    <div class="one-third">
                        <?php the_post_thumbnail('professorPortrait'); ?>
                    </div>
                    <div class="two-thirds"> 
                        <!-- heart box on professor page -->
                        <?php 
                        $likeCount = new WP_Query(array(
                            'post_type' => 'like',
                            'meta_query' => array(
                                array(
                                    'key' => 'liked_professor_id',
                                    'compare' => '=',
                                    'value' => get_the_ID()
                                )
                            )
                        ));

                        $existStatus = 'no';
                        if (is_user_logged_in()) {}
                        $existQuery = new WP_Query(array(
                            'author' => get_current_user_id(),
                            'post_type' => 'like',
                            'meta_query' => array(
                                array(
                                    'key' => 'liked_professor_id',
                                    'compare' => '=',
                                    'value' => get_the_ID()
                                )
                            )
                        ));

                        if ($existQuery->found_posts) {
                            $existStatus = 'yes';
                        }
                        ?>
                        <span class="like-box" data-like="<?php echo $existQuery->posts[0]->ID; ?>" data-professor="<?php the_ID(); ?>" data-exists=<?php echo $existStatus?>>
                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                            <i class="fa fa-heart-o" aria-hidden="true"></i>
                            <i class="fa fa-heart" aria-hidden="true"></i>
                            <span class="like-count"><?php echo $likeCount->found_posts; ?></span>
                        </span>
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            <!-- relating a professor to a program (one to many) -->
            <!-- a Related Program already created through Events CPT -->
            <?php 
                // leveraging function from ACF
                // takes as an argument the field name generated in ACF
                // this field returns an array
                $relatedPrograms = get_field('related_programs');

                if ($relatedPrograms) {
                    // style the list
                    echo '<hr class="section-break">';
                    echo '<h2 class="headline headline--medium">Subject(s) Taught</h2>';
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
<?php 

require get_theme_file_path('/inc/like-route.php');
require get_theme_file_path('/inc/search-route.php');

// Add Custom Field to the REST API
function university_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {
            return get_the_author();
        }
    ));

    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() {
            return count_user_posts(get_current_user_id(), 'note');
        }
    ));
}

add_action('rest_api_init', 'university_custom_rest');

function pageBanner($args = NULL){
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }

    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!$args['photo']) {
        if (get_field('page_banner_background_image') AND !is_archive() AND !is_home() ) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }

    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)"></div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
                <div class="page-banner__intro">
                <p><?php echo $args['subtitle']?></p>
            </div>
        </div>
    </div>
<?php } // close of pageBanner()

function university_files() {
    //wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyDin3iGCdZ7RPomFLyb2yqFERhs55dmfTI', NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));

    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        // enable the nonce upon successful log in
        'nonce' => wp_create_nonce('wp_rest')
    ));
} // close of university_files()
add_action('wp_enqueue_scripts', 'university_files'); 
// 2nd param the name of the function for WP to hook into 

function university_features(){
    add_theme_support('title-tag');
    // enable featured image; not a default behavior
    add_theme_support('post-thumbnails');
    // creating diff image sizes for featured image
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features'); /* The name of the action that we want to hook onto*/

/* parameter is the WordPress Query object */
function university_adjust_queries($query) {

    //if (!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()) {
       // $query->set('posts_per_page', -1);
      //}

    // query manipulation to order 
    if (!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
    
    // query manipulation to order events
    if (!is_admin() AND is_post_type_archive('event') AND /* not manipulate a custom query */$query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                /* use variable and declare above */
                'value' => $today,
                /* set type of what comparing */
                'type' => 'numeric'
            )
            ));
    }
}// close of university_adjust_queries()
add_action('pre_get_posts', 'university_adjust_queries');

// function universityMapKey($api) {
//     $api['key'] = 'yourKeyGoesHere';
//     return $api;
//   }
  
//   add_filter('acf/fields/google_map/api', 'universityMapKey');

// Redirect subscriber accounts of admin and onto home page

add_action('admin_init', 'redirectSubsToFrontEnd');

function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();

    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
    $ourCurrentUser = wp_get_current_user();

    if (count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
       show_admin_bar(false);
    }
}

// Customize Login Screen
add_filter('login_header_url', 'ourHeaderUrl');

function ourHeaderUrl() {
    return esc_url(site_url('/'));
}

add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('university_extra_styles', get_theme_file_uri('/build/index.css'));
}

add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle() {
    return get_bloginfo('name');
}

// Force note posts to be private
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2); // filter hook runs for every post and every post type

function makeNotePrivate($data, $postarr) { // from every post type to note post type
    // Sanitize Title and Content fields/unfiltered HTML -> plain text
    if ($data['post_type'] == 'note') { // limit # of posts
        if (count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
            die("You have reached your note limit");
        }

        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    if ($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = "private";
    }
    
    return $data;
}

// set up to ignore files with All-In-One WP Migrate Plugin
// add_filter('ai1wm_exclude_content_from_export', 'ignoreCertainFiles');

// function ignoreCertainFiles($exclude_filters) {
//     $exclude_filters[] = 'themes/fictional-university-theme/node_modules';

//     return $exclude_filters;
// }


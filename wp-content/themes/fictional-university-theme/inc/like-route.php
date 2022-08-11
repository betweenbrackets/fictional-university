<?php
// Like.js server-side functions live here
// single-professor.php has the HTML

add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data) {
    if (is_user_logged_in()) {

        $professor = sanitize_text_field($data['professorId']);

        $existQuery = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(
                array(
                    'key' => 'liked_professor_id',
                    'compare' => '=',
                    'value' => $professor
                )
            )
        ));

        if ($existQuery -> found_posts == 0 AND get_post_type($professor) == 'professor') {
            return wp_insert_post(array(
                'post_type' => 'like',
                'post_status' => 'publish',
                'post_title' => '2nd PHP Test',
                'meta_input' => array(
                    'liked_professor_id' => $professor
                )
            ));
        } else {
            die("Invalid Professor ID");
        }
    } else {
        die("Only logged in users can create a like.");
    }
}

function deleteLike($data) { // to access the data array, include as a parameter
    $likeId = sanitize_text_field($data['like']); // match the property that you're sending from your JavaScript/Like.js: currentLikeBox.attr("data-like", '');
    // >>> Is like the post_type value that we set up in creating the Like CPT?

    // with custom API need to be strict about testing
    if (get_current_user_id() == get_post_field('post_author', $likeId) AND get_post_type($likeId) == 'like') { // only delete if you're the one that created it AND the ID of the post type that they're trying to delete = like
        wp_delete_post($likeId, true);

        return 'Congrats, like deleted.';
    } else {
        die("You do not have permission to delete that.");
    }
}
?>
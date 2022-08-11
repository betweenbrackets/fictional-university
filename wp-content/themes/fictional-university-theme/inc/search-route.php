<?php

// custom REST API with WP data formatted as JSON
// functionality for the search overlay
// formats the JSON returned

add_action('rest_api_init', 'universityRegisterSearch');

function universityRegisterSearch() {
    //params: namespace, route, array
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE, // aka 'GET'
        'callback' => 'universitySearchResults'
    )); 
}

function universitySearchResults($data) {
    // Custom Query to get all the results
    // Search based on keyword
   $mainQuery = new WP_Query(array(
       'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
       // access any parameter that user adds to the url
       // WP function to sanitize user input from MySQL attack
        's' => sanitize_text_field($data['term'])
   ));
   // the results array
   $results = array(
       // the sub arrays or filter arrays
       // the array returned to our javascript
       'generalInfo' => array(),
       'professors' => array(),
       'programs' => array(),
       'events' => array(),
       'campuses' => array()
   );
    //loop to push to the array
   while($mainQuery->have_posts()) {
        $mainQuery->the_post();
        // funnels results into specific arrays
        if (get_post_type() == 'post' || get_post_type() == 'page') {
            array_push($results['generalInfo'], array(
                // create an array of the desired values to return
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            ));
        } // end of if statement

        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                // create an array of the desired values to return
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
            ));
        } // end of if statement

        if (get_post_type() == 'program') {
            // TODO Campus Custom Post Type
            // $relatedCampuses = get_field('related_campus');
            // if ($relatedCampuses) {
            //     foreach($relatedCampuses as $campus) {
            //         array_push($reuslt['campuses'], array(
            //             'title' => get_the_title($campus),
            //             'peramlink' => get_the_permalink($campus)
            //         ));
            //     }
            // }
            array_push($results['programs'], array(
                // create an array of the desired values to return
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id'=>get_the_id()
            ));
        } // end of if statement
        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                // create an array of the desired values to return
                'title' => get_the_title(),
                'permalink' => get_the_permalink()
            ));
        } // end of if statement
        if (get_post_type() == 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18);
            }

            array_push($results['events'], array(
                // create an array of the desired values to return
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        } // end of if statement
   } // end of while loop

   if ($results['programs']) {
        $programsMetaQuery = array('relation' => 'OR');

        foreach($results['programs'] as $item) {
            array_push($programsMetaQuery, array(
                'key' => 'related_programs', // ACF value
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'
            ));
        }
        // custom query to relate programs and professors in search results
        // search based on a relationship
        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programsMetaQuery
        ));
    
        while($programRelationshipQuery->have_posts()) {
            $programRelationshipQuery->the_post();

            if (get_post_type() == 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                $description = null;
                if (has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words(get_the_content(), 18);
                }
    
                array_push($results['events'], array(
                    // create an array of the desired values to return
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => $description
                ));
            } // end of if statement
    
            if (get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    // create an array of the desired values to return
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));
            } // end of if statement
        } // end of while loop
 
        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
   } // end of if statement
   return $results;
} // end of universitySearchResults();


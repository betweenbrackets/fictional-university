// like-route.php client-side functions
// single-professor.php has the HTML

// access jQuery
import $ from 'jquery';

class Like {
    // 1. describe and create/initiate our object
    constructor() {
        this.events();
    }
    // 2. events
    events() {
        $(".like-box").on("click", this.ourClickDispatcher.bind(this));
    }

   // 3. methods (function, action...)
   ourClickDispatcher(e) {
       var currentLikeBox = $(e.target).closest(".like-box");
       // the data method pulls in the one value on page load
       // if (currentLikeBox.data('exists') == 'yes')
       // the attr method pulls in updated values as user updates/clicks the heart to like and unlike
       if (currentLikeBox.attr('data-exists') == 'yes') {
        this.deleteLike(currentLikeBox);
       } else {
           this.createLike(currentLikeBox);
       }
   }

   createLike(currentLikeBox) {
    $.ajax({
        // NONCE 
        beforeSend: (xhr) => {
            xhr.setRequestHeader('X-WP-Nonce', universityData.nonce );
        },
        url: universityData.root_url + '/wp-json/university/v1/manageLike',
        type: 'POST',
        data: {'professorId': currentLikeBox.data('professor')},
        success: (response) => {
            // filling in the heart
            currentLikeBox.attr('data-exists', 'yes');
            // updating the number
            var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
            likeCount++;
            // printing the number
            currentLikeBox.find(".like-count").html(likeCount);
            // getting the ID number of the post (response)
            currentLikeBox.attr("data-like", response);
            console.log(response);
        },
        error: (response) => {
            console.log(response);
        }
    });
   }

   deleteLike(currentLikeBox) {
    $.ajax({
        // NONCE 
        beforeSend: (xhr) => {
            xhr.setRequestHeader('X-WP-Nonce', universityData.nonce );
        },
        url: universityData.root_url + '/wp-json/university/v1/manageLike', // custom endpoint
        data: {'like': currentLikeBox.attr('data-like')},
        type: 'DELETE',
        success: (response) => {
            // filling in the heart
            currentLikeBox.attr('data-exists', 'no');
            // updating the number
            var likeCount = parseInt(currentLikeBox.find(".like-count").html(), 10);
            likeCount--;
            // updating the HTML count/what prints
            currentLikeBox.find(".like-count").html(likeCount);
            // not getting the ID number of the post (response) when deleting
            currentLikeBox.attr("data-like", '');
            console.log(response);
        },
        error: (response) => {
            console.log(response);
        }
    });
   }
} // end of Like class

export default Like;
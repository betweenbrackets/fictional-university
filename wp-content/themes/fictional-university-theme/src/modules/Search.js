// This file is the custom search overlay in the header
// This is the front-end javascript for the search overlay

// Axios approach not working - TODO troubleshoot
// See my-notes.scss


import $ from "jquery"

class Search {
  // 1. describe and create/initiate our object
  constructor() {
    this.addSearchHTML();
    this.resultsDiv = $("#search-overlay__results")
    this.openButton = $(".js-search-trigger")
    this.closeButton = $(".search-overlay__close")
    this.searchOverlay = $(".search-overlay")
    this.searchField = $("#search-term");
    this.events()
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
  }

  // 2. events
  events() {
    // the on method resets the "this" value to the event that triggers and not the object so therefore bind
    this.openButton.on("click", this.openOverlay.bind(this));
    this.closeButton.on("click", this.closeOverlay.bind(this));
    $(document).on("keydown", this.keyPressDispatcher.bind(this));
    this.searchField.on("keyup", this.typingLogic.bind(this));
  }

  // 3. methods (function, action...)
  typingLogic() {
    if (this.searchField.val() != this.previousValue) {
      clearTimeout(this.typingTimer);

      if (this.searchField.val()) {
        if (!this.isSpinnerVisible) {
          this.resultsDiv.html('<div class="spinner-loader"></div>');
          this.isSpinnerVisible = true;
        }
        this.typingTimer = setTimeout(this.getResults.bind(this), 750);
      } else {
        this.resultsDiv.html('');
        this.isSpinnerVisible = false;
      }
    }
    this.previousValue = this.searchField.val();
  }

  getResults() {
//     So we no longer need to worry about the synchronous versus asynchronous topic because we no longer need
// to make multiple JSON requests.
// So up on our new line of code, we no longer need to use the when then combo.
// So let's say dollar sign to use J query and then let's use the JSON method.
// It takes two arguments.
// The first is the URL that you want to send a request to, let's just say X as a placeholder.
// And the second argument is a function that you want to run once the server or you URL response.
// So let's just apply an anonymous function here.
// And whatever JSON data this you URL responds with will be passed into this function.
// So within these parentheses, we want to be sure to include a parameter that can receive the incoming data.
// Before we move on, though, I do want to be sure to use an S6 Arrow function.
// So that way the value of this keyword will not be modified within this function.

// TODO Create Campus Custom Post Type with image
    $.getJSON(universityData.root_url + '/wp-json/university/v1/search?term=' + this.searchField.val(), (results) => {
      this.resultsDiv.html(`
        <div class='row'>
          <div class ='one-third'>
            <h2 class = "search-overlay__section-title">General Information</h2>
            ${results.generalInfo.length ? '<ul class = "link-list min-list">' : '<p>No match for that search.</p>'}
            ${results.generalInfo.map(item => `<li><a href = "${item.permalink}">${item.title}</a> ${item.postType == 'post' ? `by ${item.authorName}` : ''}</li>`).join('')}
            ${results.generalInfo.length ? '</ul>' : ''}
          </div>
          <div class ='one-third'>
            <h2 class = "search-overlay__section-title">Programs</h2>
            ${results.programs.length ? '<ul class = "link-list min-list">' : `<p>No match for that search. <a href="${universityData.root_url}/programs">View all programs.</a></p>`}
            ${results.programs.map(item => `<li><a href = "${item.permalink}">${item.title}</a></li>`).join('')}
            ${results.programs.length ? '</ul>' : ''}

            <h2 class = "search-overlay__section-title">Professors</h2>
            ${results.professors.length ? '<ul class = "professor-cards">' : `<p>No match for that search.</p>`}
            ${results.professors.map(item => `
              <li class="professor-card__list-item">
                <a class="professor-card" href="${item.permalink}">
                    <img class="professor-card__image" src="${item.image}">
                    <span class="professor-card__name">
                      ${item.title} 
                    </span>
                </a>
            </li>
            `).join('')}
            ${results.professors.length ? '</ul>' : ''}
          </div>
          <div class ='one-third'>
            <h2 class = "search-overlay__section-title">Campuses</h2>
            ${results.programs.length ? '<ul class = "link-list min-list">' : `<p>No match for that search.<a href="${universityData.root_url}/programs">View all campuses.</a></p>`}
            ${results.programs.map(item => `<li><a href = "${item.permalink}">${item.title}</a></li>`).join('')}
            ${results.programs.length ? '</ul>' : ''}
            
            <h2 class = "search-overlay__section-title">Events</h2>
            ${results.events.length ? '' : `<p>No match for that search.<a href="${universityData.root_url}/events">View all events.</a></p>`}
            ${results.events.map(item => `
              <div class="event-summary">
                <a class="event-summary__date t-center" href="${item.permalink}">
                    <!-- call the return of the ACF function  (get_field) from Admin Panel -->
                    <span class="event-summary__month">${item.month}</span>
                    <span class="event-summary__day">${item.day}</span>
                </a>
                <div class="event-summary__content">
                    <h5 class="event-summary__title headline headline--tiny"><a href="${item.permalink}">${item.title}</a></h5>
                    <p>${item.description} <a href="${item.permalink}" class="nu gray">Learn more</a></p>
                </div>
              </div> 
            `).join('')}
          </div>
        </div>
      `); // close of template literal
      this.isSpinnerVisible = false;
    }); // close of getJSON()

  } // end of getResults

  keyPressDispatcher(e) {
    if (e.keyCode == 83 && !this.isOverlayOpen && !$("input, textarea").is(":focus")) {
      this.openOverlay();
    }
    if (e.keyCode == 27 && this.isOverlayOpen) {
      this.closeOverlay();
    }
  }

  openOverlay() {
    this.searchOverlay.addClass("search-overlay--active")
    $("body").addClass("body-no-scroll");
    this.searchField.val('');
    setTimeout(() => this.searchField.focus(), 301);
    this.isOverlayOpen = true;
    return false; // prevents default behavior of anchor element on search icon in header.php 
  }

  closeOverlay() {
    this.searchOverlay.removeClass("search-overlay--active")
    $("body").removeClass("body-no-scroll");
    this.isOverlayOpen = false;
  }

  addSearchHTML() {
    // jQuery to append a template literal
    $("body").append(`
        <div  class = "search-overlay">
          <div class="search-overlay__top">
            <div class="container">
              <i class ="fa fa-search search-overlay__icon" aria-hidden="true"></i>
              <input type="text" class="search-term" placeholder ="What are you looking for?" id="search-term" autocomplete="off"></input>
              <i class ="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
            </div>
          </div>
          <div class = "container">
            <div id="search-overlay__results"></div>
          </div>
        </div>
    `);
  }
} // Close of Search class

export default Search

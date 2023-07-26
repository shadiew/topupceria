"use strict";

$("form").on('submit', function (e) {
    //calling stripe function
    pay();
    //stop form submission
    e.preventDefault();
  });
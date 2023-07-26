"use strict";

var options = {
    placeholder: 'Waiting for your content',
    theme: 'snow',
    modules: {
              toolbar: [
                '#toolbar'
              ]
          }
  };
  
  var editor1 = new Quill('#editor-terms_ins', options);
  var editor2 = new Quill('#editor-privacy_ins', options);
  
  function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
  
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }
  
  var form = document.querySelector('form');
  form.onsubmit = function() {
    // Populate hidden form on submit
    var termsHtml = editor1.root.innerHTML;
    var privacyHtml = editor2.root.innerHTML;

    var terms = document.querySelector('input[name=terms_ins]');
    terms.value = escapeHtml(termsHtml);
  
    var privacy = document.querySelector('input[name=privacy_ins]');
    privacy.value = escapeHtml(privacyHtml);
  }
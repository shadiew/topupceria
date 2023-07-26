"use strict";

var options = {
    placeholder: 'Waiting for your content',
    theme: 'snow',
    modules: {
              toolbar: [
                  ['bold', 'italic', 'underline'],
                  ['link', 'blockquote', 'code-block'],
                  [{
                      list: 'ordered'
                  }]
              ]
          }
  };
  
  var editor = new Quill('#editor-new_order', options);
  var editor2 = new Quill('#editor-manual_deposit', options);
  var editor3 = new Quill('#editor-paypal_ins', options);
  var editor4 = new Quill('#editor-paytm_ins', options);
  var editor5 = new Quill('#editor-stripe_ins', options);
  
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
    var new_ordHtml = editor.root.innerHTML;
    var man_depoHtml = editor2.root.innerHTML;
    var paypalHtml = editor3.root.innerHTML;
    var paytmHtml = editor4.root.innerHTML;
    var stripeHtml = editor5.root.innerHTML;
  
    var new_ord = document.querySelector('input[name=new_order_ins]');
    new_ord.value = escapeHtml(new_ordHtml);
  
    var man_depo = document.querySelector('input[name=manual_deposit_ins]');
    man_depo.value = escapeHtml(man_depoHtml);
  
    var paypal = document.querySelector('input[name=paypal_ins]');
    paypal.value = escapeHtml(paypalHtml);
  
    var paytm = document.querySelector('input[name=paytm_ins]');
    paytm.value = escapeHtml(paytmHtml);

    var stripe = document.querySelector('input[name=stripe_ins]');
    stripe.value = escapeHtml(stripeHtml);
  }
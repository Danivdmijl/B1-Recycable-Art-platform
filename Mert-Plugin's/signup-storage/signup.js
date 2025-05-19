jQuery(document).ready(function ($) {
  $('.signup-form').on('submit', function (e) {
    e.preventDefault();
    var email = $(this).find('input[type="email"]').val();

    $.post(signup_ajax_obj.ajax_url, {
      action: 'save_signup_email',
      email: email,
      _ajax_nonce: signup_ajax_obj.nonce,
    }, function (response) {
      if (response.success) {
        alert(response.data);
      } else {
        alert('Fout: ' + response.data);
      }
    });
  });
});

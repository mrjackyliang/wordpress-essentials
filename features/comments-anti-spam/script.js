/**
 * Script.
 */
jQuery(function ($) {
  $(function () {
    anti_spam_init();
  });

  // Support for comments forms loaded via ajax.
  $(document).ajaxSuccess(function () {
    anti_spam_init();
  });

  // Anti-spam initialization.
  function anti_spam_init() {
    // Retrieve session key.
    let session = $("input#session").val();
    let session64 = btoa(session);

    // Set "session64" value into "token" value.
    $("input#token").val(session64);
  }
});

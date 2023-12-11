jQuery('.notice-dismiss').on('click', function(e) {
    jQuery('.settings-error').remove();
});	
jQuery('.close-btn').on('click', function(e) {
    jQuery('.error-div').remove();
});	

jQuery('.check-git-status').on('click', function(e) {
    var data = {
        action: 'get_status',
    };
    $.ajax({
        url: ajaxurl,
        data:data,
        cache: false,
        
        success: function(html){
          $(".results-div").html(html);
        }
      });

});
jQuery('.git-pull-request').on('click', function(e) {
    var _wpnonce = $('#wp_pull_nonce').val();
    var data = {
        action: 'git_pull',
        _wpnonce:_wpnonce,
    };
    $.ajax({
        url: ajaxurl,
        data:data,
        cache: false,
        
        success: function(html){
          $(".results-div").html(html);
        }
      });
});

jQuery('.git-push-request').on('click', function(e) {
    var _wpnonce = $('#wp_push_nonce').val();
    var data = {
        action: 'git_push',
        _wpnonce:_wpnonce,
    };
    $.ajax({
        url: ajaxurl,
        data:data,
        cache: false,
        
        success: function(html){
          $(".results-div").html(html);
        }
      });
});

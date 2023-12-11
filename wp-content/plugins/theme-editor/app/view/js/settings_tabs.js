jQuery(document).ready(function($){
       jQuery("#tabs").tabs();	
       jQuery('li.ui-tabs-tab').on("click", function(){
              var href = jQuery(this).find('a').attr('href');
              localStorage.setItem('theme_editor_selected_tab',href);
       });  
});  
      
// JavaScript Document

	jQuery(document).ready(function(){
		var mobheader = '<div class="mob-header"><span class="mob_button"><i class="fa fa-bars"></i></span></div>';
		jQuery('div#menu').prepend(mobheader);
		
	  jQuery("div#menu").on('click', 'span.mob_button', (function(){
		  jQuery(this).removeClass('mob_button').addClass('mob_button2').html('<i class="fa fa-times" aria-hidden="true"></i>');
		jQuery("#menu > ul").slideDown();
	  }));
	  
	  jQuery("div#menu").on('click', 'span.mob_button2', (function(){
		  jQuery(this).removeClass('mob_button2').addClass('mob_button').html('<i class="fa fa-bars"></i>');
		jQuery("#menu > ul").slideUp();
	  }));

	});
	
	
	
	 jQuery(window).scroll(function() {
    if (jQuery(document).scrollTop() > 100) {
      jQuery("#menu").addClass("shrink");
    } else {
      jQuery("#menu").removeClass("shrink");
    }
  });

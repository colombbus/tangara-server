if (typeof jQuery === 'undefined') { throw new Error('Tangara\'s JavaScript requires jQuery') }

(function($){
    $(function() {
        $('#local-tuto').hide();
	$('#icon-close').click(function(){
		console.log("close");
		$('#local-tuto').animate({
			left: '-245px'
			}, 200, function(){$('#local-tuto').hide();});
		$('#local-frame').animate({
			'padding-left': '0px'
			},200);
	 });
	 
      $('#icon-open').click(function(){
	  console.log("open");
                  $('#local-tuto').show();
		  $('#local-tuto').animate({
				'left': '0'
				}, 200);
		  $('#local-frame').animate({
			  'padding-left': '285px'
			  },200);
	 });
     });
}(jQuery));
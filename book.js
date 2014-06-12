
$(document).ready(function(){
	
	$('nav ul li a').hover(
	function() {
		$(this).css("background-color","#4494C9");
	}, function() {
		$(this).css("background-color","#DA820A");
	});
	
	$('.create_profile').hover(
	function() {
		$(this).css("background-color","#4494C9");
	}, function() {
		$(this).css("background-color","#DA820A");
		
	});
	
	// $('#bookinfo').hide();
	
	$('#bookinfo #close').click( function() {
		$('#bookinfo').hide();
	});
	
	
//	$('.info').click( function(str) {
//		alert($(this).attr('id'));
//	});

	$('.info').click( function(str) {
		$.getJSON("getbook.php?q="+$(this).attr('id'), function(str)	{
			$('.account').html(str.title);
			$('.jauthor').html(str.author);
			$('.jdescription').html(str.description);
			$('.jisbn').html(str.isbn);
			$('.jgenre').html(str.genre);
			$('.jformat').html(str.readformat);
			if (str.series.length > 0) {
				if (typeof str.seriesNo !== 'undefined' && str.seriesNo !== '') {
					$('.jseries').html('Series: ' + str.series + ' #' + str.seriesNo); 
				}
				else {
					$('.jseries').html('Series: ' + str.series); 
				}
			}
			else {
				$('.jseries').html(''); 
			}
			if (str.status === 'Read') {
                $('.jnote').html('Book read in ' + str.read + '.');
            }
            else if (str.status === 'In Progress') {
            	$('.jnote').html('Currently reading book in ' + str.read + '.');
			}
			else {
				$('.jnote').html('Book is on my wish list.');
			}
			if (str.owned == 1) {
				$('.jnote').append(' I own this book.');
			}
			$('.rateit').rateit('value', str.rating);
			$('#edit').attr('href', 'add.php?id=' + str.ID);
			$('#delete').attr('href', 'del.php?id=' + str.ID);
			$('#bookinfo').show();
		});
	});
});



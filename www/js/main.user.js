$(function(){
	$.nette.ext({
		load: function () {
			$('#acolyte tr td.ms').click(function(e){
				e.preventDefault();
				$.nette.ajax({
					url:'?do=acolyte',
					data: {
						date:$(this).parent().data('date'),
						time:$(this).data('time'),
						id:$(this).data('id'),
						name:$('#acolyteName').val()
					}
				});
			});
			$('#priest tr td.ms').click(function(e){
				e.preventDefault();
				$.nette.ajax({
					url:'?do=priest',
					data: {
						date:$(this).parent().data('date'),
						time:$(this).data('time'),
						id:$(this).data('id'),
						name:$('#priestName').val()
					}
				});
			});
		}
	});
	$.nette.init();
});

$('[data-confirm]').click(function(){
	return confirm($(this).data().confirm);
});

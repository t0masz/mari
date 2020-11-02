/**
 * User scripts - for logged users
 *
 * Version 2018-02-10
 */

$(function($, undefined){
	$.nette.ext('confirm', {
		load: function () {
			$('[data-confirm]').click(function(e){
				if($(this).data('id')>0) {
					e.preventDefault();
					var question = $(this).data('confirm');
					if (question) {
						if (!confirm(question)) {
							e.stopImmediatePropagation();
							return false;
						} else {
							return true;
						}
					}
				}
			});
		}
	});
	$.nette.ext('setData', {
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
			$('#intention tr td.ms').click(function(e){
				e.preventDefault();
				if($('#code').val()>0) {
					$.nette.ajax({
						url:'?do=intention',
						data: {
							date:$(this).parent().data('date'),
							time:$(this).data('time'),
							id:$(this).data('id'),
							intention:$('#intention').val(),
							amount:$('#amount').val(),
							code:$('#code').val(),
						}
					});
					$('#intention').val('');
					$('#amount').val('');
					$('#code').val('');
				} else {
					alert('Není vyplněné heslo!');
				}
			});
		}
	});
	$('input[data-dateinput-type]').dateinput({
		datetime: {
			dateFormat: 'd.m.yy',
			timeFormat: 'H:mm'
		},
		'datetime-local': {
			dateFormat: 'd.m.yy',
			timeFormat: 'H:mm'
		},
		date: {
			dateFormat: 'd.m.yy'
		},
		month: {
			dateFormat: 'MM yy'
		},
		week: {
			dateFormat: "w. 'týden' yy"
		},
		time: {
			timeFormat: 'H:mm'
		},
	});
	
	$.nette.init();
});

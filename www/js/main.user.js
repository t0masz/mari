/**
 * User scripts - for logged users
 *
 * Version 2017-02-18
 */

$(function($, undefined){
	$.nette.ext('confirm', {
		load: function () {
			$('[data-confirm]').click(function(e){
				e.preventDefault();
				var ret = true;
				if ($(this).data().id > 0) {
					ret = confirm($(this).data().confirm);
				}
				if (ret === true) {
					return true;
				} else {
					e.stopImmediatePropagation();
					return false;					
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

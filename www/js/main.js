/**
 * User scripts
 *
 * Version 2015-05-01
 */

$(document).ready(function(){
	$('.bxslider').bxSlider({
		captions: true,
		minSlides: 2,
		maxSlides: 5,
		slideWidth: 250,
		slideMargin: 10	
	});
	$('.fancybox').fancybox();
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
});

$(function($, undefined){
	$.nette.ext('bs-modal', {
		init: function() {
			// if the modal has some content, show it when page is loaded
			var $modal = $('#modal');
			if ($modal.find('.modal-content').html().trim().length !== 0) {
				$modal.modal('show');
			}
		},
		success: function (jqXHR, status, settings) {
			if (typeof settings.responseJSON.snippets != 'undefined') {
				var $snippet = settings.responseJSON.snippets['snippet--modal'];
			}
			if (!$snippet) {
				return;
			}
			var $modal = $('#modal');
			if ($modal.find('.modal-content').html().trim().length !== 0) {
				$modal.modal('show');
			} else {
				$modal.modal('hide');
			}
		}
	});
	
	$(function () {
		$.nette.init();
	});
});
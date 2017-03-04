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

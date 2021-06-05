/**
 * THK Analytics - free/libre analytics platform
 *
 * @copyright Copyright (C) 2015 Thought is free.
 * @link http://thk.kanzae.net/analytics/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @author LunaNuko
 *
 * This program has been developed on the basis of the Research Artisan Lite.
 */

jQuery(function($){
	$("#mopen").click(function(){
		$("body").append('<div id="overlay"></div>');
		$("#overlay").slideDown("fast");

		centeringModalSyncer();
		$("#modal").slideDown("fast");
		$("#overlay, #mclose").unbind().click(function(){
			$("#modal, #overlay").slideUp("fast",function(){
				$('#overlay').remove();
			});
		});
	});

	$(window).resize(centeringModalSyncer);

	function centeringModalSyncer(){
		var w = $(window).width();
		var h = $(window).height();
		var cw = $("#modal").outerWidth({margin:true});
		var ch = $("#modal").outerHeight({margin:true});

		$("#modal").css({"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"})
	}
});

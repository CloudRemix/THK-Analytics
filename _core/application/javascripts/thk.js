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

/**
 * Research Artisan Lite: Website Access Analyzer
 * Copyright (C) 2009 Research Artisan Project
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 */
jQuery(function($) { $('#main-menu').smartmenus();});

function disableButton(form) {
	var elements = form.elements;
	for (var i = 0; i < elements.length; i++) {
		if (elements[i].type == 'submit' && elements[i].name != 'download') {
			elements[i].disabled = true;
		}
	}
	form.submit();
	return false;
}
function checkboxAllOn(formId, checkBoxName) {
	var i;
	var object = document.getElementById(formId);
	if( object==undefined ) return;
	if( object.length ) {
		for( i = 0; i < object.length; i++ ) if( object[i].name == checkBoxName ) object[i].checked = true;
	}
	else {
		object.checked = true;
	}
}
function checkboxAllOff(formId, checkBoxName) {
	var i;
	var object = document.getElementById(formId);
	if( object==undefined ) return;
	if( object.length ) {
		for( i = 0; i < object.length; i++ ) if( object[i].name == checkBoxName ) object[i].checked = false;
	}
	else {
		object.checked = false;
	}
}

jQuery(document).ready(function($) {
	$('.mobile-nav').click(function(){
		header_menu = $('ul#main-menu');
		if (header_menu.css('display') == 'none') {
			header_menu.slideDown();
		} else{
			header_menu.slideUp();
		};
	});
});

function PrintPreview() {
	window.print();
	return;

	if(window.ActiveXObject == null || document.body.insertAdjacentHTML == null){
		window.print();
	} else {
		var sWebBrowserCode = '<object width="0" height="0" classid="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></object>'; 
		document.body.insertAdjacentHTML('beforeEnd', sWebBrowserCode); 
		var objWebBrowser = document.body.lastChild;
		if(objWebBrowser == null) return;
		try {
			objWebBrowser.ExecWB(7, 1);
			document.body.removeChild(objWebBrowser);
		} catch(e) {
			window.print();
		}
	}
	return;
}

function pagereload(){
	$.keepPosition.reload();
}

var autoload = false;
var timecount = 0;
if( location.hash.indexOf("at") !== -1 ){
	autoload = true;
}
jQuery(window).load(function () {
	loadrun();
});
function loadrun() {
	var hash = location.hash;
	var key = [
		["30", "30s", "30秒"],
		["60", "60s", "60秒"],
		["300", "300s", "5分"],
		["600", "600s", "10分"],
		["900", "900s", "15分"],
		["1200", "1200s", "20分"],
		["1800", "1800s", "30分"]
	];
	var opt = '';
	var def = (hash === "") ? true : false;
	for( var i=0; i < key.length; i++ ){
		if( def && key[i][0] === "60") {
			opt += '<option value="'+key[i][0]+'" selected>'+key[i][2]+'</option>';
		}
		else {
			opt += (hash.indexOf(key[i][1]) !== -1) ? '<option value="'+key[i][0]+'" selected>'+key[i][2]+'</option>' : '<option value="'+key[i][0]+'">'+key[i][2]+'</option>';
		}
	}
	var targetElem = document.getElementById("autotime");
	if( targetElem ) {
		document.getElementById("autotime").innerHTML = opt;
	}
}
function autoProcess() {
	var reloadTime = getOpt();
	var bg = document.getElementById('main');
	bg.style.borderColor = "#ff0000";

	var msg = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQBAMAAADt3eJSAAAAA3NCSVQICAjb4U/gAAAAGFBMVEXqDRPqFxzrHyTsJyzvQ0XzbG/72tr///9iO326AAAACHRSTlP/////////AN6DvVkAAAA8SURBVAiZYyiHAobyVBcgCCtnKDMQAgLmdIZSZiUgMAhnKBECMRTdKWUADRRSBBlYZiAIBEAr4JbCnAEAilUePDBQA9EAAAAASUVORK5CYII=" width="16" height="16" alt="停止" title="停止" class="button" onclick="autoStop()" />';
	document.getElementById("autoele").innerHTML = msg;

	timecount++;
	if( !autoload ) {
		location.hash = "at" + reloadTime + "s";
		autoload = true;
		$.keepPosition.reload();
	}
	if(timecount >= reloadTime){
		if( location.hash.indexOf("at") !== -1 ){
			autoload = true;
			$.keepPosition.reload();
		}
	}
}
function getOpt(){
	var sel = document.getElementById('autotime');
	var opt = document.getElementById('autotime').options;
	return opt.item(sel.selectedIndex).value;
}
function autoStart() {
	timecount = 0;
	autoID = setInterval(autoProcess,1000);
	return false;
}
function autoStop() {
	autoload = false;
	timecount = 0;
	clearInterval( autoID );

	var bg = document.getElementById('main');
	bg.style.borderColor = "#ccc";

	var msg = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQBAMAAADt3eJSAAAAA3NCSVQICAjb4U/gAAAAGFBMVEUFBAQYGBguLi5dXV2rq6vf39/8/Pz///+7gqxMAAAACHRSTlP/////////AN6DvVkAAABQSURBVAiZYygvLy8D4nKGcif3sjQQo0yBObwsHcxgALNADAaR9PR0MINBpRzKYDAthzIYw9EZMClViDkMKmUQhnB6GdSKdLClzu7lENuhAADyoiPSi55xywAAAABJRU5ErkJggg==" width="16" height="16" alt="自動更新" title="自動更新" class="button" onclick="autoStart()" />';
	document.getElementById("autoele").innerHTML = msg;
	location.hash = "";
}
if( autoload || ( location.hash.indexOf("at") !== -1 )){
	window.onload = autoStart;
}

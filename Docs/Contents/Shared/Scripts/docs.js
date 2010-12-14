// Remy Sharp's HTML5 enabling script
// For discussion and comments, see: http://remysharp.com/2009/01/07/html5-enabling-script/
(function(){if(!/*@cc_on!@*/0)return;var e='abbr,article,aside,audio,canvas,datalist,details,eventsource,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video'.split(','),i=e.length;while(i--){document.createElement(e[i])}})();


(function($){

var url_title_re = new RegExp('([\\w-]+)\.html'),
	titlePrefix = document.title,
	$nav = $('#nav'),
	$content = $('#content'),
	$selectedLink;


/**
 * Select Link
 */
var selectLink = function($link, updateHash){
	if ($selectedLink) $selectedLink.removeClass('selected');
	$selectedLink = $link.addClass('selected');

	var title = $link.html().replace(/&amp;/g, '&');
	document.title = titlePrefix+' - '+title;

	var url = $link.attr('href');

	if (updateHash) {
		var hash = url.match(url_title_re)[1];
		document.location.replace(document.location.pathname+'#'+hash);
	}

	$content.attr('src', url);
};


if (document.location.hash) {
	var $link = $('a[href$='+document.location.hash.substr(1)+'.html]', $nav);

	if ($link.length) {
		selectLink($link);
	}
}

if (! $selectedLink) {
	selectLink($('a:first', $nav));
}


$('a', $nav).click(function(event){
	event.preventDefault();
	selectLink($(this), true);
});


})(jQuery);

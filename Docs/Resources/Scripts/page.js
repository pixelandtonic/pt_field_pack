var ee_affiliate_name = 'brandonkelly',
	external_re = new RegExp('^https?://'),
	external_ee_re = new RegExp('^(https?://(secure\\.|www\\.)?expressionengine.com\\/)([^#]*)(#(\\w+))?$'),
	ee_affiliate_re = new RegExp('^index\\.php\\?affiliate='+ee_affiliate_name),
	relative_img_re = new RegExp('/\\.\\./Images/(.*)$');

$('img').each(function(){
	var match = this.src.match(relative_img_re);

	if (match) {
		this.src = 'Contents/Local/Images/'+match[1];
	}
});

$('a').each(function(){
	// is this an external link?
	if (this.href.match(external_re)) {
		if (! this.target) this.target = '_blank';

		// if this is a link to expressionengine.com
		// but not already an affiliate link, convert it one
		var href = this.href,
			match = href.match(external_ee_re);

		if (match && ! match[3].match(ee_affiliate_re)) {
			this.href = match[1]+'index.php?affiliate='+ee_affiliate_name
			          + (match[3] ? '&page=/'+match[3] : '')
			          + (match[5] ? '&anchor='+match[5] : '');
		}
	}
});

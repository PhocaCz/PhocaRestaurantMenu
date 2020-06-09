/* Take a screenshot when clicking on button */
jQuery(document).ready(function(){
	jQuery(document).on("click",".phPrintButton",function(event) {  
		
		
		var phLang	= Joomla.getOptions('phLangPRM');

		var id = jQuery(this).data("id");
		var idIframe = '#' + id + ' iframe';
		var box = '#phocarestaurantmenu';
		
		var alertBox = jQuery(idIframe).contents().find(".alert")[0];
		
		if (alertBox !== undefined) {
			jQuery(alertBox).remove();
		}

		var html = jQuery(idIframe).contents().find("html")[0];
		jQuery(html).scrollTop(0);

		
		var body = jQuery(idIframe).contents().find(box)[0];
		
		html2canvas(body, {scrollY: -window.scrollY}).then(function(canvas) {
			//document.body.appendChild(canvas);
		 
			try {
				canvas.toBlob(blob => navigator.clipboard.write([new ClipboardItem({"image/png": blob})]));
				jQuery(html).prepend('<div class="alert alert-success">' + phLang['COM_PHOCAMENU_SUCCESS_IMAGE_COPIED'] + '</div>');
				
				
			} catch(err) {
				//console.error(err.name, err.message);
				jQuery(html).prepend('<div class="alert alert-error alert-danger">' + phLang['COM_PHOCAMENU_ERROR_IMAGE_COPIED'] + '<br>(' + err.name + ' ' + err.message + ')</div>');
				
			}          
		});
	  
		event.preventDefault();

	});
});

function prmAddStyleToHeader(header) {

	
	var phVars	= Joomla.getOptions('phVarsPRM');
	
	
	// Remove stylesheet 
	var removeA = phVars['remove_stylesheet_string'];
	if(removeA.length < 1 || removeA == undefined){
		//empty
	} else {

		for (var i = 0, len = removeA.length; i < len; i++) {
			var removeString = "link[href*='" + removeA[i] + "']";
			jQuery(header).find(removeString).remove();
		}
	}
	
	var styles = "<style>"  + phVars['css']  + "</style>";
	jQuery(styles).appendTo(header);
}
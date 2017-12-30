
jQuery(window).ready(function(){
    // Target the #main id for fitVids    
	Vids_Fit_and_Box();
});

//function fitVidsAll(){
        //var _html = jQuery("#go_wrapper").html();
        //console.log(_html);
//        jQuery("body").fitVids();     
//}

function fitVidsNow(){
        //var _html = jQuery("#go_content").html();
        //console.log(_html);
        //jQuery("#go_content").fitVids(); 
//        var fitvids_switch = jQuery("#go_wrapper").data('fitvids');
//        if (fitvids_switch === 'On'){
//        	jQuery("body").fitVids(); 
//        }
        jQuery("body").fitVids(); 
        
   
}

function Max_width_and_LightboxNow(){  
        //do stuff
		//add a max width video wrapper to the fitVid
		var _maxwidth = jQuery("#go_wrapper").data('maxwidth');
        var fluid_width_video_wrapper = {};
        jQuery('[class^="fluid-width-video-wrapper"]').each(function(){
        jQuery(this).wrap('<div class="max-width-video-wrapper" style="position:relative;"><div>');
        
        jQuery(".max-width-video-wrapper").css("max-width", _maxwidth);
        }); 
    	
    	//Toggle lightbox on and off based on option
    	var lightbox_switch = jQuery("#go_wrapper").data('lightbox');
		//alert (lightbox_switch);
    	if (lightbox_switch === 'On'){
    	
			//add a featherlight lightroom wrapper to the fitvids
			var max_width_video_wrapper = {};
			jQuery('[class^="max-width-video-wrapper"]').each(function(){
				jQuery(this).prepend('<a style="display:block;" class="featherlight_wrapper" href="#" data-featherlight-iframe-width="100%" data-featherlight-iframe-height="100%" data-featherlight="iframe" ><span style="position:absolute; width:100%; height:100%; top:0; left: 0; z-index: 1;"></span></a>');
			});
	
			//adds a html link to the wrapper for featherlight lightbox
			var featherlight_wrapper = {};
			jQuery('[class^="featherlight_wrapper"]').each(function(){
				var _src = jQuery(this).parent().find('iframe').attr('src');
				jQuery(this).attr("href", _src);
				var _href = jQuery(this).attr("href");
				jQuery(this).attr("href", _href + '?&autoplay=1');
				//activates the lightbox
				jQuery.featherlight.defaults.closeOnClick = true;
				jQuery.featherlight.defaults.iframeWidth = '100%';
				jQuery.featherlight.defaults.iframeHeight = '100%';
				jQuery(this).featherlight();
			});
        } 
 }
 
 
function Vids_Fit_and_Box(){
    function runmefirst(callback) {
        fitVidsNow();
        callback();
    };
    runmefirst(function() {
        Max_width_and_LightboxNow();
    });
};

/*
function Vids_Fit_and_Box_Submit(){
   function runmefirst(callback) {
        fitVidsNew();
        callback();
    };
    runmefirst(function() {
        LightboxNow();
    });
};
*/


////Saving this snippet just in case
/*function Vids_Fit_and_Box(){
    promise = FitVidsAllFirst().then(LightBoxSecond);      
}

function Vids_Fit_and_Box_Submit(){
    promise = FitVidsNewFirst().then(LightBoxSecond);         
}

function FitVidsAllFirst(){
    d = new jQuery.Deferred();
    fitVidsAll()
    setTimeout('d.resolve()',0);
    return d.promise()
}

function FitVidsNewFirst(){
    d = new jQuery.Deferred();
    fitVidsNew()
    setTimeout('d.resolve()',0);
    return d.promise()
}
    
function LightBoxSecond(){
    d = new jQuery.Deferred();
    LightboxNow()
    setTimeout('d.resolve()',0);
    return d.promise()
}
*/

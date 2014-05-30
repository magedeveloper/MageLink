jQuery.noConflict();

/* prepare login details */
function prepareLogin()
{
    var form = jQuery("form#login-form");
	
    hideLoader();

    displayLoader(form, "");
    
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        //jsonp: "jsonp",
        url: ajaxPrepareUrl, 
        params:{
        }, 
        data: jQuery(form).serializeArray(),
        success: function(response){
            if (response)
            {
                // No Errors
                hideLoader();

                if (response.status == "success")
                {
                    tx_magelink_ajax_init_login(response);
                }
            
            }
        }
        
    });	
    		
}

/* init typo3 login procedure */
function tx_magelink_ajax_init_login(data)
{
	var form = jQuery("form#login-form");
    var loginUrl = data.url;

    displayLoader(form, "");

    jQuery.ajax({
        type: "GET",
        dataType: "jsonp",
        jsonp: "jsonp",
        url: loginUrl,
        params:{},
        data: {
            'tx_magelink_loginform[enc]' : data.enc
        },
        success: function(data){
        }
    });
	
}

/* finalizes the login */
function tx_magelink_ajax_complete_login(data)
{
	if (data.status == "success")
	{
		var url = baseUrl + data.url;
		
	    jQuery.ajax({
	        type: "POST",
	        dataType: "json",
	        //jsonp: "jsonp",
	        url: url, 
	        params:{
	        }, 
	        data: data,
	        success: function(response){
	            if (response)
	            {
	                if (response.status == "success")
	                {
	                    tx_magelink_ajax_response(response);
	                }
	                else
	                {
	                	tx_magelink_ajax_add_flash_message(response.message);
	                	
	                	hideLoader();
	                }
	            
	            }
	        }
	        
	    });	
		
	}
	
}

/* receives a response from TYPO3 */
function tx_magelink_ajax_response(data)
{
    jQuery.ajax({
        type: "GET",
        dataType: "jsonp",
        jsonp: "jsonp",
        url: ajaxResponseUrl,
        params:{},
        data: {
            'tx_magelink_loginform[enc]' : data.enc
        },
        success: function(data){
        }
    });
    
}

/* LAST STEP IN LOGIN PROCEDURE FINALIZES ALL DATA */
function tx_magelink_ajax_finalize_login(data)
{
	hideLoader();
	
	if (data)
	{
		if (data.redirect != "")
		{
			window.location.href = data.redirect;
		}
		else
		{
			window.location.reload();
		}
	}
}


function tx_magelink_ajax_add_flash_message(message)
{
	alert(message);
}

function displayLoader(element, message)
{
	var loaderImage = "<img src=\""+skinUrl + "images/opc-ajax-loader.gif\""+" border=\"0\" class=\"v-middle\" />";
    jQuery(element).append("<div class=\"tx-magelink-loader\"><span class=\"please-wait\">"+loaderImage+message+"</span></div>");
}

function hideLoader()
{
  jQuery("div.tx-magelink-loader").remove();
}

function fadeOutLoader()
{
    jQuery("div.tx-magelink-loader").delay(2000).fadeOut("slow");
}

/* sprintf equivalent */
function sprintf(format, etc) 
{
    var arg = arguments;
    var i = 1;
    return format.replace(/%((%)|s)/g, function (m) { return m[2] || arg[i++] })
}
/* prepare languge translations */
locallang = {
    translations: [],
    set: function(key, value) {
        this.translations[key] = value;
    },
    setMultiple: function(list) {
        for(el in list) {
            this.translations[list[el][key]] = list[el][value];
        }
    },
    translate: function(key) {
        return this.translations[key];
    }
}

/* sprintf equivalent */
function sprintf(format, etc) 
{
    var arg = arguments;
    var i = 1;
    return format.replace(/%((%)|s)/g, function (m) { return m[2] || arg[i++] })
}

/* adds a flash message container right after the body tag */
function prepareBody()
{
    var $div = jQuery('<div />').prependTo('body');
    $div.attr('id', 'tx-magelink-messages');
}

/* displays an ajax loader */
function displayLoader(element, message)
{
    if (message != "")
    {
        message = "<span class=\"loader-text\">"+message+"</span>";
    }

    jQuery(element).append("<div class=\"tx-magelink-loader\"><div class=\"loader-image\"></div>"+message+"</div>");
}

/* hides the ajax loader */
function hideLoader()
{
    jQuery("div.tx-magelink-loader").remove();
}

/* fades out the ajax loader */
function fadeOutLoader()
{
    jQuery("div.tx-magelink-loader").delay(2000).fadeOut("slow");
}

jQuery(document).ready(function() {

    /* center function for screen elements */
    jQuery.fn.center = function () {
        this.css("position","absolute");
        this.css("top", Math.max(0, ((jQuery(window).height() - jQuery(this).outerHeight()) / 2) +
            jQuery(window).scrollTop()) + "px");
        this.css("left", Math.max(0, ((jQuery(window).width() - jQuery(this).outerWidth()) / 2) +
            jQuery(window).scrollLeft()) + "px");
        return this;
    }
	
    prepareBody();
    tx_magelink_ajax_getcart();
    
    /* login form listener */
    jQuery("form#tx-magelink-login").submit(function(e)
    {
        e.preventDefault();
        tx_magelink_ajax_login_prepare(this);
    });
    
});


/* add item to cart */
function tx_magelink_ajax_addtocart(entityId)
{
    var allowSubmit = true;
    
    jQuery("form#add").submit(function(e) 
    {
        if (!allowSubmit) return false;
        allowSubmit = false;
            
        e.preventDefault();
        
        var options = jQuery(this).find("select.super-attribute-select");
        
        if (!tx_magelink_ajax_validate_options(options))
        {
            return false;
        }

        displayLoader(this, '');

        var magentoUrl = jQuery("input#magentoUrl").val();
        var url = magentoUrl + "magelink/json/cart/add/product/id/"+entityId+"?"+jQuery(this).serialize();

        jQuery.ajax({
            type: "GET",
            dataType: "jsonp",
            jsonp: "jsonp",
            jsonpCallback: "tx_magelink_ajax_addtocart_callback",
            url: url,
            params:{},
            data: {
            },
            success: function(data){
            },
            error: function(data){
                tx_magelink_ajax_add_flash_message( locallang.translate("could_not_add_product_to_cart"), 'error', true );
            }
        });
        
    });
}

/* callback for tx_magelink_ajax_addtocart */
function tx_magelink_ajax_addtocart_callback(data)
{
	if (data.type && data.message)
	{
		tx_magelink_ajax_add_flash_message( data.message, data.type, false );
	}
	else
	{
		tx_magelink_ajax_add_flash_message( locallang.translate("could_not_add_product_to_cart"), 'error', true );
	}
	
	tx_magelink_ajax_getcart();
	
}

/* retrieve cart contents */
function tx_magelink_ajax_getcart()
{
    var magentoUrl = jQuery("input#magentoUrl").val();
    var url = magentoUrl + "magelink/json/cart/get/";
    
    jQuery.ajax({
        type: "GET",
        dataType: "jsonp",
        jsonpCallback: 'tx_magelink_ajax_getcart_callback',
        jsonp: "jsonp",
        url: url, 
        params:{}, 
        data: {
        },
        success: function(data){
        },
        error: function(data){
           //tx_magelink_ajax_add_flash_message( locallang.translate("could_not_retrieve_cart"), 'error', true );
        }
    });
}


/* callback for tx_magelink_ajax_getcart_callback */
function tx_magelink_ajax_getcart_callback(data)
{
	var magentoUrl = jQuery("input#magentoUrl").val();
    var cartContentDiv = jQuery("div.tx-magelink-cart-contents");
    
    displayLoader(cartContentDiv, locallang.translate("refreshing_cart"));

    jQuery.ajax({
        type: "POST",
        url: "index.php",
        params:{}, 
        data: {
            'eID': 'magelinkAjax',     
            'vendorName': 'MageDeveloper',
            'extensionName': 'Magelink',     
            'pluginName': 'Cartdisplay',
            'controllerName' : 'Cart',
            'actionName' : 'show',
            'arguments' :{                       
                cart : data
            }
        },
        success: function(response){
            jQuery(cartContentDiv).html(response);
            hideLoader();
        },
        error: function(data){
            tx_magelink_ajax_add_flash_message( locallang.translate("could_not_refresh_cart"), 'error', true );
        }
    });
    
}


/* remove item from cart */
function tx_magelink_ajax_removefromcart(entityId)
{
    var cartContentDiv = jQuery("div.tx-magelink-cart-contents");
    var magentoUrl = jQuery("input#magentoUrl").val();
    var url = magentoUrl + "magelink/json/cart/remove/product/id/"+entityId;
    
    displayLoader(cartContentDiv, locallang.translate("removing_product_from_cart"));

    jQuery.ajax({
        type: "GET",
        dataType: "jsonp",
        jsonp: "jsonp",
        jsonpCallback: 'tx_magelink_ajax_removefromcart_callback',
        url: url, 
        params:{}, 
        data: {
        },
        success: function(data){
        },
        error: function(data){
            tx_magelink_ajax_add_flash_message( locallang.translate("could_not_remove_from_cart"), 'error', true );
        }
    });
}

/* callback for tx_magelink_ajax_removefromcart */
function tx_magelink_ajax_removefromcart_callback(data)
{
	if (data.type && data.message)
	{
		tx_magelink_ajax_add_flash_message( data.message, data.type, false );
	}
	else
	{
		tx_magelink_ajax_add_flash_message( locallang.translate("could_not_remove_from_cart"), 'error', true );
	}	
	
	tx_magelink_ajax_getcart();
}

/* forgot password */
function tx_magelink_ajax_forgot_password()
{
    var form        = jQuery("form#tx-magelink-forgot-password");
    var magentoUrl  = jQuery("input#magentoUrl").val();
    var email       = jQuery(form).find("input#tx-magelink-login-email").val();
    var url         = magentoUrl + "magelink/json/forgotPassword/";
    
    displayLoader(form, locallang.translate("please_wait"));

    jQuery.ajax({
        type: "GET",
        dataType: "jsonp",
        jsonp: "jsonp",
        jsonpCallback: 'tx_magelink_ajax_forgot_password_callback',
        url: url,
        params:{
            email:email
        },
        data: {
            email:email
        },
        success: function(data){
        },
        error: function(data){
            tx_magelink_ajax_add_flash_message( locallang.translate("could_not_call_forgot_password"), 'error', true );
        }
    });
    
}

/* forgot password */
function tx_magelink_ajax_forgot_password_callback(data)
{
	if (data.type && data.message)
	{
		tx_magelink_ajax_add_flash_message( data.message, data.type, true );
	}
	else
	{
		tx_magelink_ajax_add_flash_message( locallang.translate("could_not_call_forgot_password"), 'error', true );
	}	
}

/* login */
function tx_magelink_ajax_login_prepare(form)
{
    hideLoader();

    displayLoader(form, locallang.translate("preparing_login"));

    jQuery.ajax({
        type: "POST",
        dataType: "json",
        //jsonp: "jsonp",
        url: ajaxPrepareUrl, 
        params:{
        }, 
        data: { 
           'tx_magelink_loginform[arguments]' : jQuery(form).serializeArray()
        },
        success: function(response){
            if (response.type == 'success')
            {
                // No Errors
                hideLoader();

                if (response)
                {
                    tx_magelink_ajax_init_login(response);
                }
                else
                {
                    tx_magelink_ajax_add_flash_message( locallang.translate("response_broken"), response.type, true );
                }
            
            }
            else
            {
                tx_magelink_ajax_add_flash_message(response.message, response.type, true);
            }
            
        },
        error: function(data){
            tx_magelink_ajax_add_flash_message( locallang.translate("login_could_not_be_prepared"), 'error', true );
        }
        
    });
    
}

/* callback function */
function tx_magelink_ajax_init_login(data)
{
    var loginUrl = data.url;
    var form =  jQuery("form#tx-magelink-login");

    displayLoader(form, locallang.translate("calling_shop_login"));

    jQuery.ajax({
        type: "GET",
        dataType: "jsonp",
        jsonp: "jsonp",
        jsonpCallback: 'tx_magelink_ajax_complete_login_callback',
        url: loginUrl,
        params:{},
        data: {
            enc : data.enc
        },
        success: function(data){
        },
        error: function(data){
            tx_magelink_ajax_add_flash_message( locallang.translate("login_could_not_be_initialized"), 'error', true );
        }
    });

}

function tx_magelink_ajax_complete_login_callback(data)
{
    var form =  jQuery("form#tx-magelink-login");

    displayLoader(form, locallang.translate("evaluating_response"));
    
    if (data.type == "success")
    {
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: ajaxResponseUrl,
            params:{
            },
            data: {
                'tx_magelink_loginform[arguments]' : data
            },
            success: function(response){
                hideLoader();
                if (response)
                {   
                    if (response.user)
                    {
                        displayLoader(form, sprintf(locallang.translate("logged_in_as"), "<strong>"+response.user.email+"</strong>"));
                    }
                    
                    fadeOutLoader();
                    form.html(response.html);
                }
                else
                {
                    tx_magelink_ajax_add_flash_message( locallang.translate("response_broken"), "error", true );
                }
                
            }
        });
    
    }
    else
    {
    	console.log(data);
    	if (data.message && data.type == "error")
    	{
    		tx_magelink_ajax_add_flash_message( data.message, 'error', true );
    	}
    	else
    	{
    		tx_magelink_ajax_add_flash_message( locallang.translate("login_could_not_be_initialized"), 'error', true );
    	}
    }
    
}

/* validate product options */
function tx_magelink_ajax_validate_options(options)
{
    var validated = true;

    jQuery(options).each(function(){
        if (!this.name || !this.value)
        {
            jQuery(this).addClass("required");
            validated = false;
        }
        else
        {
            if (jQuery(this).hasClass("required"))
            {
                jQuery(this).removeClass("required");
            }
        }
        
    });
    
    return validated;
}


/* displays the callback message from json */
function tx_magelink_ajax_add_flash_message(message, type, closeByClick)
{
    var messageTimeout = 3000;

    if (!type)
    {
        type = "info";
    }
    
    if (type == "error")
    {
    	closeByClick = true;
    }

    var closeDiv = "";
    
    if (closeByClick)
    {
       closeDiv = "<div class=\"tx-magelink-message-close\">X</div>";
    }
    
    var messageDiv      = "<div class=\"tx-magelink-message tx-magelink-message-type-"+type+"\">"+closeDiv+"<span>"+message+"</span></div>";
    var flashMessageDiv = jQuery("div#tx-magelink-messages");
   
    //flashMessageDiv.center();
    jQuery(flashMessageDiv).html(messageDiv);

    flashMessageDiv.fadeIn();
    hideLoader();
    
    if (closeByClick == true)
    {
        flashMessageDiv.click(function() {
            jQuery(flashMessageDiv).fadeOut();
        });
    }
    else
    {
        flashMessageDiv.delay(messageTimeout).fadeOut("slow");
    }
   
}
































var ProductConfiguration = function(config) {

    var state = [];

    var settings = jQuery(".super-attribute-select");
    var values = [];
    config.prices.currentPrice = config.prices.basePrice;

    // Set default values from config
    if (config.defaultValues) {
        values = config.defaultValues;
    }

    // Overwrite defaults by url
    var separatorIndex = window.location.href.indexOf('#');
    if (separatorIndex != -1) {
        var paramsStr = window.location.href.substr(separatorIndex+1);
        var urlValues = paramsStr.toQueryParams();
        if (!values) {
            values = {};
        }
        for (var i in urlValues) {
            values[i] = urlValues[i];
        }
    }

    // Overwrite defaults by inputs values if needed
    if (config.inputsInitialized) {
        values = {};
        settings.each(function(element) {
            if (element.value) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                values[attributeId] = element.value;
            }
        }.bind(this));
    }

    var childSettings = [];
    for(var i=settings.length-1;i>=0;i--)
    {
        var prevSetting = settings[i-1] ? settings[i-1] : false;
        var nextSetting = settings[i+1] ? settings[i+1] : false;

        if (i == 0)
        {
            fillSelect(settings[i]);
        }
        else
        {
            settings[i].disabled = true;
        }

        settings[i].childSettings  = jQuery(childSettings).clone();
        settings[i].prevSetting    = prevSetting;
        settings[i].nextSetting    = nextSetting;
        childSettings.push(settings[i]);

    }

    settings.on("change", function(element){
        configure(element);
    });
    
    // fill state
    for (var i = 0; i < settings.length;i++)
    {
        var element = settings[i];
        var attributeId = element.id.replace(/[a-z]*/, '');
        
        if (attributeId && config.attributes[attributeId])
        {
            element.config = config.attributes[attributeId];
            element.attributeId = attributeId;
            state[attributeId] = false;
        }    
    }



    configureForValues();
    
    function configure(event)
    {
        var element = event.currentTarget;
        configureElement(element);
    }

    function configureElement(element)
    {
        reloadOptionLabels(element);
        
        if (element.value)
        {
            state[element.config.id] = element.value;
            
            
            if (element.nextSetting)
            {
                element.nextSetting.disabled = false;
                fillSelect(element.nextSetting);
                resetChildren(element.nextSetting);
            }
        
        }
        else
        {
            resetChildren(element);
        }
                
        reloadPrice();
        
    }
    
    function configureForValues()
    {
        if (this.values) {
            settings.each(function(element){
                var attributeId = element.attributeId;
                element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
                configureElement(element);
            }.bind(this));
        }
    }
    
    /* get attribute options */
    function getAttributeOptions(attributeId)
    {
        if (config.attributes[attributeId])
        {
            return config.attributes[attributeId].options;
        }
    }
    
    function reloadOptionLabels(element)
    {
        var selectedPrice;
        var price = jQuery(element.options[element.selectedIndex]).attr("price");
        
        if (element.options[element.selectedIndex] && price)
        {
            selectedPrice = parseFloat(price);
        }
        else
        {
            selectedPrice = 0;
        }
        
        for (var i=0;i<element.options.length;i++)
        {
            if(element.options[i].config){
                element.options[i].text = getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);
            }
        }
        
    }
    
    function getOptionLabel(option, price)
    {
        var price = parseFloat(price);
           
        var str = option.label;
        
        if (price){
                str+= ' ' + formatPrice(price, true);
        }
        return str;
    }

    function formatPrice(price, showSign)
    {
        var str = '';
        
        price = parseFloat(price);
        
        if(showSign){
            if(price<0){
                str+= '-';
                price = -price;
            }
            else{
                str+= '+';
            }
        }

        var roundedPrice = (Math.round(price*100)/100).toString();
        
        if (config.prices && config.prices[roundedPrice]) {
            str+= config.prices[roundedPrice];
        }
        else {
            str+= price.toFixed(2);
        }
        
        if (config.prices.currency)
        {
            str+= ' ' + config.prices.currency;
        }
        
        str = str.replace('.', config.prices.decSep);
        
        return str;
    }
    
    
    function fillSelect(element)
    {
        var attributeId = element.id.replace(/[a-z]*/, '');
        var options = getAttributeOptions(attributeId);
    
        clearSelect(element);
    
        element.options[0] = new Option(config.chooseText, "");
    
        var prevConfig = false;
        if (element.prevSetting)
        {
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }
        
        if (options)
        {
            var index = 1;
            
            for(var i=0;i<options.length;i++)
            {
                var allowedProducts = [];
                
                if (prevConfig) 
                {
                    for(var j=0;j<options[i].products.length;j++)
                    {
                        if(prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1)
                        {
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                }
                else 
                {
                    allowedProducts = options[i].products;
                }
                
                if(allowedProducts.length > 0)
                {
                    options[i].allowedProducts = allowedProducts;
                    
                    element.options[index] = new Option(getOptionLabel(options[i], options[i].price), options[i].id);
                    if (typeof options[i].price != 'undefined') {
                        element.options[index].setAttribute('price', options[i].price);
                    }
                    element.options[index].config = options[i];
                    index++;
                }
            
            }
            
        }
    
    }
    
    function resetChildren(element)
    {
        if(element.childSettings) 
        {
            for(var i=0; i < element.childSettings.length; i++)
            {
                for (var j=0; j < childSettings.length; j++)
                {
                    if (element.childSettings[i].id == childSettings[j].id)
                    {
                        childSettings[j].selectedIndex = 0;
                        childSettings[j].disabled = true;
                        
                        if(element.config)
                        {
                            state[element.config.id] = false;
                        }
                    }
                
                
                }
            }
            
        }
    }
    
    
    
    /* clears a select element */
    function clearSelect(element)
    {
        for (var i=element.options.length-1;i>=0;i--)
        {
            element.remove(i);
        }
    }
    
    function reloadPrice()
    {
        if (config.disablePriceReload) 
        {
            return;
        }
        
        var price    = 0;
        var oldPrice = 0;
        
        for(var i=settings.length-1;i>=0;i--)
        {
            var selected = settings[i].options[settings[i].selectedIndex];
            
            if(selected.config)
            {
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.oldPrice);
            }

            //jQuery("div.price").html(price);
        }
        
        price = price + config.prices.basePrice;


        //console.log("reloadPrice-price:"+price);
        //console.log("reloadPrice-oldPrice:"+oldPrice);
        
        //price = price + " " + config.prices.currency;
        //oldPrice = oldPrice + " " + config.prices.currency;
        
        price = formatPrice(price);
        
        
        
        reloadOldPrice();
    }
    
    function reloadOldPrice()
    {
        if (config.disablePriceReload) 
        {
            return;
        }

        var price = parseFloat(config.prices.basePrice);
        var newPrice = price;
        
        for(var i=settings.length-1;i>=0;i--)
        {
            var selected = settings[i].options[settings[i].selectedIndex];
            
            if(selected.config)
            {
                var newP = parseFloat(selected.config.price);
                
                if (newP)
                {
                    price+= newP;
                }
                
            }
            
        }

        if (price < 0)
        {
            price = 0;
        }

        price = formatPrice(price);

        jQuery("div#price_"+config.productId).html(price);
    }    
    
    
    
}













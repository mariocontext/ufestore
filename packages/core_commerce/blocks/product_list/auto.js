var productListBlock ={ 
	
	servicesDir: $("input[name=blockToolsDir]").val(),
	
	init:function(){
		$('#ccm-productListBlock-externalTarget').click(function(){  productListBlock.showResultsURL(this);  });
		$('select[name=data_source]').change(function () { $('div.product-list-data-source-pane').hide(); $('div#product-list-data-source-'+this.value).show() } );
		$('select[name=data_source]').change();
		this.blockForm=document.forms['ccm-block-form']; 
		this.tabSetup();
		this.loadPreview();		 

	},
	tabSetup: function(){
		$('ul#ccm-blockEditPane-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-blockEditPane-tab-','');
				productListBlock.showPane(pane);
			}
		});		
	}, 
	showPane:function(pane){
		$('ul#ccm-blockEditPane-tabs li').each(function(num,el){ $(el).removeClass('ccm-nav-active') });
		$(document.getElementById('ccm-blockEditPane-tab-'+pane).parentNode).addClass('ccm-nav-active');
		$('div.ccm-blockEditPane').each(function(num,el){ el.style.display='none'; });
		$('#ccm-blockEditPane-'+pane).css('display','block');
		if(pane=='preview') this.loadPreview();
	}, 
	loadPreview:function(){
		//var loaderHTML = '<div style="padding: 20px; text-align: center"><img src="' + CCM_IMAGE_PATH + '/throbber_white_32.gif"></div>';
		//$('#ccm-blockEditPane-preview').html(loaderHTML);
		var qStr=$(this.blockForm).formSerialize();
		$('#ccm-blockEditPane-preview iframe').attr('src',this.servicesDir+'preview_pane.php?'+qStr);
		/*
		$.ajax({ 
			url: this.servicesDir+'preview_pane.php?'+qStr,
			success: function(msg){ $('#ccm-blockEditPane-preview').html(msg); }
		});
		*/
	},
	showResultsURL:function(cb){
		if(cb.checked) $('#ccm-productListBlock-resultsURL-wrap').css('display','block');
		else $('#ccm-productListBlock-resultsURL-wrap').css('display','none');
	},
	
	pathSelector:function(el){
		var f=$('#ccm-block-form').get(0);
		var isOther=0;
		for( var i=0; i<f.baseSearchPath.length; i++ ){
			if( f.baseSearchPath[i].id=='baseSearchPathOther' && f.baseSearchPath[i].checked ){
				isOther=1;
				break;
			}
		}
		if( isOther ) 
			 $('#basePathSelector').css('display','block');
		else $('#basePathSelector').css('display','none');
	},
	validate:function(){
			var failed=0; 
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	}	
}

ccm_coreCommerceProductBlockSelectThumbnailOption = function(obj) {
	if (!obj) {
		obj = $("input[name=displayImage]");
	}
	
	if ($(obj).attr('checked')) {
		$(obj).parent().find('div.ccm-core-commerce-product-block-image-fields').show();
	} else {
		$(obj).parent().find('div.ccm-core-commerce-product-block-image-fields').hide();
	}
}

ccm_coreCommerceProductBlockSelectAddToCart = function(obj) {
	if (!obj) {
		obj = $("input[name=displayAddToCart]");
	}
	
	if ($(obj).attr('checked')) { 
		$("input[name=displayQuantity]").attr('disabled', false);
		$("input[name=addToCartText]").attr('disabled', false);
		$("input[name=addToCartText]").css('color', '#333');
	} else {
		$("input[name=displayQuantity]").attr('checked', false);
		$("input[name=displayQuantity]").attr('disabled', true);
		$("input[name=addToCartText]").attr('disabled', true);
		$("input[name=addToCartText]").css('color', '#aaa');
	}
}

$(function(){ 
	productListBlock.init(); 

	$("input[name=displayImage]").unbind();
	$("input[name=displayImage]").click(function() {
		ccm_coreCommerceProductBlockSelectThumbnailOption(this);
	});
	
	$('#useOverlaysC').unbind();
	$('#useOverlaysC').click(function() {
		ccm_coreCommerceProductBlockSelectThumbnailOption(this);
	});
	
	$('#useOverlaysL').unbind();
	$('#useOverlaysL').click(function() {
		ccm_coreCommerceProductBlockSelectThumbnailOption(this);
	});
	
	$("input[name=displayAddToCart]").unbind();
	$("input[name=displayAddToCart]").click(function() {
		ccm_coreCommerceProductBlockSelectAddToCart(this);
	});

	ccm_coreCommerceProductBlockSelectThumbnailOption();
	ccm_coreCommerceProductBlockSelectThumbnailOption($('#useOverlaysL'));
	ccm_coreCommerceProductBlockSelectThumbnailOption($('#useOverlaysC'));
	ccm_coreCommerceProductBlockSelectAddToCart();
	ccm_setupGridStriping('ccm-core-commerce-product-attribute-grid');
});
 
ccmValidateBlockForm = function() { return productListBlock.validate(); }
ccm_selectSitemapNode = function(cID, cName) {
	$("#searchUnderCID").val(cID);	
}

var ccm_coreCommerceActiveProductField = '';
ccm_coreCommerceSetupSearch = function() {
	
	ccm_setupAdvancedSearch('core-commerce-product');
	
	$("#ccm-core-commerce-product-list-cb-all").click(function() {
		if ($(this).attr('checked') == true) {
			$('td.ccm-core-commerce-product-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', false);
		} else {
			$('td.ccm-core-commerce-product-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-core-commerce-product-list-cb input[type=checkbox]").click(function(e) {
		if ($("td.ccm-core-commerce-product-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-core-commerce-product-list-multiple-operations").attr('disabled', true);
		}
	});

	ccm_setupInPagePaginationAndSorting();
	ccm_setupSortableColumnSelection();
}

ccm_coreCommerceSetupOrderSearch = function() {
	ccm_setupAdvancedSearchFields('core-commerce-order');
	ccm_setupSortableColumnSelection();
}

ccm_coreCommerceLaunchProductSelector = function(selector) {
	ccm_coreCommerceActiveProductField = selector;
	ccm_coreCommerceLaunchProductManager();
}

ccm_coreCommerceLaunchProductManager = function() {
	$.fn.dialog.open({
		width: '90%',
		height: '70%',
		modal: false,
		href: ccm_coreCommerceProductManagerURL,
		title: 'Product Search'
	});
}

ccm_coreCommerceSelectProduct = function(productID) {
	ccm_coreCommerceTriggerSelectProduct(productID);
}

ccm_coreCommerceTriggerSelectProduct = function(productID, af) {
	if (af == null) {
		var af = ccm_coreCommerceActiveProductField;
	}
	//alert(af);
	var obj = $('#' + af + "-core-commerce-product-selected");
	var dobj = $('#' + af + "-core-commerce-product-display");
	dobj.hide();
	obj.show();
	obj.load(ccm_coreCommerceProductManagerSelectorDataURL + '?productID=' + productID + '&ccm_core_commerce_product_selected_field=' + af, function() {
		/*
		// old file manager stuff kept here in case we need to change how some of this works
		
		obj.attr('fID', fID);
		obj.attr('ccm-file-manager-can-view', obj.children('div').attr('ccm-file-manager-can-view'));
		obj.attr('ccm-file-manager-can-edit', obj.children('div').attr('ccm-file-manager-can-edit'));
		obj.attr('ccm-file-manager-can-admin', obj.children('div').attr('ccm-file-manager-can-admin'));
		obj.attr('ccm-file-manager-can-replace', obj.children('div').attr('ccm-file-manager-can-replace'));
		*/
		
		obj.click(function(e) {
			e.stopPropagation();
			ccm_coreCommerceActivateProductMenu($(this),e);
		});
		
	});
	var vobj = $('#' + af + "-core-commerce-product-value");
	vobj.attr('value', productID);
}

ccm_coreCommerceActivateProductMenu = function(obj, e) {
	// Is this a file that's already been chosen that we're selecting?
	// If so, we need to offer the reset switch
	
	var selectedProduct = $(obj).find('div[ccm-core-commerce-product-manager-field]');
	var selector = '';
	if(selectedProduct.length > 0) {
		selector = selectedProduct.attr('ccm-core-commerce-product-manager-field');
	}
	ccm_hideMenus();
	
	var productID = $(obj).attr('productID');

	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-core-commerce-product-menu" + productID + selector);
	
	if (!bobj) {
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-core-commerce-product-menu" + productID + selector;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		bobj = $("#ccm-core-commerce-product-menu" + productID + selector);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
		html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
		html += '<ul>';
		if (ccm_alLaunchType != 'DASHBOARD') {
			// if we're launching this at the selector level, that means we've already chosen a file, and this should instead launch the library
			var onclick = (selectedProduct.length > 0) ? 'ccm_coreCommerceLaunchProductSelector(\'' + selector + '\')' : 'ccm_triggerSelectFile(' + productID + ')';
			var chooseText = (selectedProduct.length > 0) ? 'Choose New Product' : 'Select';
			html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="90%" dialog-height="70%" dialog-title="' + chooseText + '" id="menuSelectFile' + productID + '" href="javascript:void(0)" onclick="' + onclick + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">'+ chooseText + '<\/span><\/a><\/li>';
		}
		if (selectedProduct.length > 0) {
			html += '<li><a class="ccm-icon" href="javascript:void(0)" id="ecMenuClearFile' + productID + selector + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/remove.png)">'+ ccmi18n.clear + '<\/span><\/a><\/li>';
		}

		/*
		
		if (ccm_alLaunchType != 'DASHBOARD' && selectedFile.length > 0) {
			html += '<li class="header"></li>';	
		}
		if ($(obj).attr('ccm-file-manager-can-view') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.view + '" id="menuView' + fID + '" href="' + CCM_TOOLS_PATH + '/files/view?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">'+ ccmi18n_filemanager.view + '<\/span><\/a><\/li>';
		} else {
			html += '<li><a class="ccm-icon" id="menuDownload' + fID + '" target="' + ccm_alProcessorTarget + '" href="' + CCM_TOOLS_PATH + '/files/download?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">'+ ccmi18n_filemanager.download + '<\/span><\/a><\/li>';	
		}
		if ($(obj).attr('ccm-file-manager-can-edit') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.edit + '" id="menuEdit' + fID + '" href="' + CCM_TOOLS_PATH + '/files/edit?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">'+ ccmi18n_filemanager.edit + '<\/span><\/a><\/li>';
		}
		html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="630" dialog-height="450" dialog-title="' + ccmi18n_filemanager.properties + '" id="menuProperties' + fID + '" href="' + CCM_TOOLS_PATH + '/files/properties?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/wrench.png)">'+ ccmi18n_filemanager.properties + '<\/span><\/a><\/li>';
		if ($(obj).attr('ccm-file-manager-can-replace') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="300" dialog-height="250" dialog-title="' + ccmi18n_filemanager.replace + '" id="menuFileReplace' + fID + '" href="' + CCM_TOOLS_PATH + '/files/replace?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/paste_small.png)">'+ ccmi18n_filemanager.replace + '<\/span><\/a><\/li>';
		}
		html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.sets + '" id="menuFileSets' + fID + '" href="' + CCM_TOOLS_PATH + '/files/add_to?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">'+ ccmi18n_filemanager.sets + '<\/span><\/a><\/li>';
		if ($(obj).attr('ccm-file-manager-can-admin') == '1' || $(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li class="header"></li>';
		}
		if ($(obj).attr('ccm-file-manager-can-admin') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="400" dialog-height="380" dialog-title="' + ccmi18n_filemanager.permissions + '" id="menuFilePermissions' + fID + '" href="' + CCM_TOOLS_PATH + '/files/permissions?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">'+ ccmi18n_filemanager.permissions + '<\/span><\/a><\/li>';
		}
		if ($(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.deleteFile + '" id="menuDeleteFile' + fID + '" href="' + CCM_TOOLS_PATH + '/files/delete?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">'+ ccmi18n_filemanager.deleteFile + '<\/span><\/a><\/li>';
		}
		*/
		
		html += '</ul>';
		html += '</div></div>';
		html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
		bobj.append(html);
		
		$("#ccm-core-commerce-product-menu" + productID + selector + " a.dialog-launch").dialog();
		
		$('a#ecMenuClearFile' + productID + selector).click(function(e) {
			ccm_coreCommerceClearProduct(e, selector);
			ccm_hideMenus();
		});

	} else {
		bobj = $("#ccm-core-commerce-product-menu" + productID + selector);
	}
	
	ccm_fadeInMenu(bobj, e);
}

ccm_coreCommerceClearProduct = function(e, af) {
	e.stopPropagation();
	var obj = $('#' + af + "-core-commerce-product-selected");
	var dobj = $('#' + af + "-core-commerce-product-display");
	var vobj = $('#' + af + "-core-commerce-product-value");
	vobj.attr('value', 0);
	obj.hide();
	dobj.show();
}
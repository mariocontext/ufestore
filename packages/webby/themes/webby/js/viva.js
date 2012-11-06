$(document).ready(function() {
    $(function(){
    

		$().UItoTop({ easingType: 'easeOutQuart' });
		
		$(window).load( function () { 
      	$("#loading").fadeOut(800); 
    	}) 
   		.end(); 
		
	});		
});

// Rounded Corners

DD_roundies.addRule('a.button', '5px', true);

// Clear Search Text

function clearText(theField)
		{
			if (theField.defaultValue == theField.value)
			theField.value = '';
		}

		function addText(theField)
		{
			if (theField.value == '')
			theField.value = theField .defaultValue;
};
var captchasallecta  =  Joomla.getOptions('captchasallecta');
//console.log('captchasallecta.js prefix = '+captchasallecta.prefix); 
//console.log('captchasallecta.js submitTimeoutValue = '+captchasallecta.submitTimeoutValue); 
//console.log('captchasallecta.js containerid = '+captchasallecta.containerid); 
//console.log('captchasallecta.js objectid = '+captchasallecta.objectid); 
//console.log('captchasallecta.js sessionFormToken = '+captchasallecta.sessionFormToken); 
//console.log('captchasallecta.js msg_error_captcha_reload = '+captchasallecta.msg_error_captcha_reload); 
//console.log('captchasallecta.js submitTimeout = '+captchasallecta.submitTimeout); 
//console.log('captchasallecta.js msg_success = '+captchasallecta.msg_success); 
//console.log('captchasallecta.js msg_loading = '+captchasallecta.msg_loading); 


if (captchasallecta.submitTimeout)
{
	jQuery(document).ready(
	function($)
	{
		$(captchasallecta.prefix+'_captchasallecta_progress_line').delay(0).queue(function ()
		{
			$(this).css('width', '100%')
		});
		var sec=0;
		var max=captchasallecta.submitTimeoutValue;
		var counter = setInterval(
			function()
			{ 
				sec++;
				if (sec > max)
				{
					clearInterval(counter);
				}
			}, 
			1000);// var counter
			
		setTimeout( function()
			{
				var container = $('#'+captchasallecta.containerid);
				var img = $('#'+captchasallecta.objectid);
				$.ajax(
				{
					url: '/index.php?option=com_ajax&group=captcha&plugin=sallecta&format=json&action=load&timeout='+sec+'&'+captchasallecta.sessionFormToken+'=1',
					type: 'POST',
					success: function(responce) 
						{
							container.append($.parseJSON(responce.data));
							$('#captchasallecta_reload').trigger('click');
							$('#captchasallecta_field-lbl').html(captchasallecta.msg_success);
							img.html('');
						},
					error: function(response)
					{
						alert(captchasallecta.msg_error_captcha_reload);
					}
				});
			}, 
			captchasallecta.submitTimeoutValue*1000
		); // setTimeout
	}); // jQuery(document).ready(function($)
} // if (captchasallecta.submitTimeout)



jQuery(document).ready(
function($)
{
	$('body').on('click', '#captchasallecta_reload', function()
	{
		var img = $('#'+captchasallecta.objectid);
		$('#captchasallecta_field-lbl').html(captchasallecta.msg_loading);
		$.ajax(
		{
			url: '/index.php?option=com_ajax&group=captcha&plugin=sallecta&format=json&action=reload&'+captchasallecta.sessionFormToken+'=1',
			type: 'POST',
			success: function(responce)
			{
				img.css('background-image', 'url(' + responce.data + ')');
				$('#captchasallecta_field-lbl').html(captchasallecta.msg_success);
			},
			error: function(response) 
			{
				alert(captchasallecta.msg_error_captcha_reload);
			}
		}); // $.ajax
	}); // $('body').on('click'
}); // $('body').on('click', '#captchasallecta_reload'

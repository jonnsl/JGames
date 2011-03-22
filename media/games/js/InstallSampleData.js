window.addEvent('domready', function(){
	document.id('InstallSampleData').addEvent('click', function(){
		var e = this;
		var request = new Request.JSON({
			url: 'index.php?option=com_games&controller=ajax&task=InstallSampleData&format=json',
			onRequest: function(){
				e.set('value', 'Installing Sample Data.');
				e.setStyle('padding-left', 18);
				e.setStyle('background', 'url("../media/games/images/ajax-loader.gif") no-repeat');
			},
			onSuccess: function(jsonResponse){
				if(jsonResponse.success == true){
					e.set('disabled', 'disabled');
					e.set('value', jsonResponse.msg);
					e.setStyle('padding-left', 15);
					e.setStyle('background', 'url("../media/games/images/OK-icon.png") no-repeat');
				} else {
					var systemMessage = document.id('system-message');
					if(systemMessage)
					{
						var dt = new Element('dt', {'class':'error','html':'Error'}).inject(systemMessage, 'bottom');
						var dd = new Element('dd', {'class':'error message','html':'<ul><li>'+jsonResponse.msg+'</li></ul>'}).inject(systemMessage, 'bottom');
					}
					else{
						var error = new Element('dl', {'id':'system-message'});
						error.set('html', '<dt class="error">Error</dt><dd class="error message"><ul><li>'+jsonResponse.msg+'</li></ul></dd>');
						error.inject(document.id('element-box'), 'before');
					}
				}
			}
		}).send();
	});
});
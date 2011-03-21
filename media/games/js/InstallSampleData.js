window.addEvent('domready', function(){
	document.id('InstallSampleData').addEvent('cilck', function(e){
		e.preventDefault();
		var request = new Request.JSON();
		request.addEvents({
			request: function(){
				this.addClass('loading');
			},
			complete: function(){
				this.removeClass('loading');
			},
			success: function(jsonResponse){
				if(jsonResponse.success == true){
					this.set('disabled', 'disabled');
					this.set('html', jsonResponse.msg);
				} else {
					var error = document.id('system-message') || new Element('dl', {'id':'system-message'});
					error.set('html', '<dt class="error">Error</dt><dd class="error message"><ul><li>'+jsonResponse.msg+'</li></ul></dd>');
					error.inject(document.id('element-box'), 'before');
				}
			}
		});
		request.send({url: 'index.php?option=com_games&controller=ajax&task=InstallSampleData&format=json'});
	});
});
window.addEvent('domready',function(){
	document.id('filters_form').getChildren().each(function(e){
		e.addEvent('change', function(){
			this.form.submit();
		});
	});
});

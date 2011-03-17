document.addEvent('domready', function(){
	$$('.select_game').each(function(e){
		e.addEvent('click', function(){
			if (window.parent) window.parent.SelectGame(this.getParent('form').field.value, this.get('rel'), this.innerHTML);
			window.parent.SqueezeBox.close();
		});
	});
});

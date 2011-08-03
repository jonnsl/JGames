window.addEvent('domready', function(){
	document.id('toolbar').getElement('ul').addEvent("click:relay(li.button a)", function(e, button){
		e.stop();
		var form = document.id('adminForm');
		form.task.set('value', button.get('data-task'));
		form.controller.set('value', button.get('data-controller'));
		form.submit();
	});
});

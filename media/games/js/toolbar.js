window.addEvent('domready', function(){
	document.id('toolbar').getElement('ul').addEvent("click:relay(li.button a)", function(event,node){
		event.preventDefault();
		var info = JSON.decode(node.get('rel'));
		var form = document.id('adminForm');
		form.task.set('value', info['task']);
		form.controller.set('value', info['controller']);
		form.submit();
	});
});

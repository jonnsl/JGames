window.addEvent('domready',function(){

	/*
	 * Auto complete
	 */
	new Meio.Autocomplete(document.id('jform_developer'),	'index.php?option=com_games&controller=ajax&w=developer&format=json');
	new Meio.Autocomplete(document.id('jform_publisher'),	'index.php?option=com_games&controller=ajax&w=publisher&format=json');
	new Meio.Autocomplete(document.id('jform_serie'),		'index.php?option=com_games&controller=ajax&w=serie&format=json');

	/*
	 * Pegi and ESRB rating behaviour
	 */
	$$('.rating').each(function(e){
		e.addEvent('click:relay(a.select)', function(event, clicked){
			event.stop();
			e.getElements('a.select.selected').removeClass('selected');
			clicked.addClass('selected');
			e.getElement('input').set('value', this.get('data-value'));
		});
	});

	/*
	 * Pegi Content behaviour
	 */
	document.id('content').addEvent('click:relay(a.select)', function(e, select){
		if(select.hasClass('selected')) {
			select.removeClass('selected');
			select.getElement('input').checked = false;
		}
		else {
			select.addClass('selected');
			select.getElement('input').checked = true;
		}
		e.stop();
	});

});
jInsertFieldValue = function(value, id){
	var input = document.id(id), div = input.getParent('div');
	input.set('value', value);
	div.getElement('img').set('src', '../'+value);
	div.getElement('a').set('href', '../'+value);
};
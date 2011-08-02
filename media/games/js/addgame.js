var horizontal;

window.addEvent('domready',function(){

	/*
	 * Auto complete
	 */
	new Meio.Autocomplete($('jform_developer'),	'index.php?option=com_games&controller=ajax&w=developer&format=json');
	new Meio.Autocomplete($('jform_publisher'),	'index.php?option=com_games&controller=ajax&w=publisher&format=json');
	new Meio.Autocomplete($('jform_serie'),		'index.php?option=com_games&controller=ajax&w=serie&format=json');

	/*
	 * Pegi and ESRB rating behaviour
	 */
	var rating = $$('.rating a.select');

	var removeClass = function(array, cls)
	{
		array.each(function(e){e.removeClass(cls);});
	};

	rating.each(function(e){
		e.addEvent('click', function(){
			this.getParent().getElements('a').each(function(e){e.removeClass('selected');});
			this.addClass('selected');
			this.getParent().getElement('input').set('value', this.rel);
			return false;
		});
	});

	/*
	 * Pegi Content behaviour
	 */
	var content = $$('#pegi #content a.select');
	content.each(function(e){
		e.addEvent('click', function(){
			if(this.hasClass('selected')) {
				this.removeClass('selected');
				this.getElement('input').checked = false;
			}
			else {
				this.addClass('selected');
				this.getElement('input').checked = true;
			}
			return false;
		});
	});


	/*$('search_esrb').addEvent('click', function(){
		var title = $('jform_title').value;
		var jSonRequest = new Request.JSON({url: 'http://localhost/trunk/administrator/index.php?option=com_games&task=ajax.autoSearchESRB&format=json&title='+title, onComplete: function(esrb){
			// @TODO update the fields
		}}).send();
	});*/
});
jInsertFieldValue = function(value, id){
	var input = $(id), div = input.getParent('div');
	input.set('value', value);
	div.getElement('img').set('src', '../'+value);
	div.getElement('a').set('href', '../'+value);
};
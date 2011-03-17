SelectGame = function(field, id, name) {
	document.id(field+'_id').set('value', id);
	document.id(field+'_name').set('value', name.trim());
};

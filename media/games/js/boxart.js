
var Boxart = new Class({

	boxarts: [],

	params: {},

	initialize: function(params, saved){
		this.params = new Hash(params);
		this.saved = new Hash(saved);
		var cls = this;
		this.platforms = document.getElementById('jform_platforms').getChildren();
		this.platforms.each(function(e, i){
				e.addEvent('click', function(){
					cls.platforms.each(function(p, k){
						cls.updateBoxarts();
					});
				});
		});
		cls.updateBoxarts();
		this.modal = SqueezeBox.initialize();
	},

	updateBoxarts: function() {
		var cls = this;
		cls.platforms.each(function(e, i){
			if(e.selected && !cls.hasBoxart(e.value)) {
				if(e.value != 0)cls.addBoxart(e.value, e.innerHTML);
			}
			if(!e.selected && cls.hasBoxart(e.value)) {
				if(e.value != 0)cls.removeBoxart(e.value);
			}
			cls.centralizeArrows();
		});
	},

	centralizeArrows: function() {
		var new_y = $('boxarts').getSize().y;
		new_y = (new_y - 40)/2;
		$('previous').setStyle('margin-top', new_y);
		$('next').setStyle('margin-top', new_y);
	},
	hasBoxart: function(id){
		return this.boxarts.contains(id);
	},

	addBoxart: function(id, name){
		var params = this.params.get(id);
		var value = this.saved.get(id);
		var image = (value == '' || typeof value == 'object') ? params.default_boxart : value ;//a imagem sera o valor passado se o mesmo não estiver vazio
		var image_thumb = image.replace(/(\.jpg)$/i, '_thumb$1');//a miniatura sera a imagem acrescida de _thumb antes da extensão

		// Create the elements
		var div = new Element('div', {'class': 'boxart_holder', 'id': 'boxart_'+id});
		var aimg = new Element('a', {'href' : '../'+image}).inject(div);
		SqueezeBox.assign(aimg);
		var img = new Element('img', {'alt': name, 'src': '../'+image_thumb, 'width': params.width, 'height': params.height}).inject(aimg);
		var div2 = new Element('div', {'class': 'clr'}).inject(div);
		var input = new Element('input', {'type':'hidden', 'id':'jform_boxart_path_'+id, 'value':value, 'name':'jform[boxarts]['+id+']'}).inject(div);
		var a  = new Element('a', {
			'class':'modal hasTip',
			'href':'index.php?option=com_media&view=images&tmpl=component&fieldid=jform_boxart_path_'+id+'&folder=',
			'rel':'{handler: \'iframe\', size: {x: 800, y: 500}}',
			'html': 'Add Boxart'
			}).inject(div);

		//TODO: Translate
		a.store('tip:title', 'Add Boxart');
		a.store('tip:text', 'Add Boxart for '+name);
		new Tips(a);

		SqueezeBox.assign(a, {parse: 'rel'});
		div.inject('boxarts_inner', 'top');

		// FIX for the panel
		$('boxarts').getParent('div').setStyle('height', '100%');

		// FIX for the carousel
		horizontal = new Fx.Scroll.Carousel('boxarts',{
			mode: 'horizontal',
			loopOnScrollEnd: true
		});

		// Track
		this.boxarts.include(id);
	},

	removeBoxart: function(id){
		var cls = this;
		$('boxart_'+id).nix({duration: 1000, onComplete:function(){cls.centralizeArrows();}}, true);
		this.boxarts.erase(id);
	}
});
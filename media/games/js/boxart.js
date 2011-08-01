
var Boxart = new Class({

	boxarts: [],
	
	container: '',
	
	previousArrow: '',
	
	NextArrow: '',

	params: {},
	
	saved: {},

	initialize: function(params, saved){
		this.params = new Hash(params);
		this.saved = new Hash(saved);
		this.container = document.id('boxarts');
		this.previousArrow = document.id('previous');
		this.NextArrow = document.id('next');
		this.select = document.id('jform_platforms');
		this.platforms = this.select.getChildren();
		this.select.addEvent('change', this.updateBoxarts.bind(this));
		this.updateBoxarts();
		this.modal = SqueezeBox.initialize();
	},

	updateBoxarts: function() {
		this.platforms.each(function(e, i){
			if (e.selected && !this.hasBoxart(e.value)) {
				if (e.value != 0) this.addBoxart(e.value, e.get('html'));
			}
			if (!e.selected && this.hasBoxart(e.value)) {
				if (e.value != 0) this.removeBoxart(e.value);
			}
			this.centralizeArrows();
		}, this);
	},

	centralizeArrows: function() {
		var new_y = this.container.getSize().y;
		new_y = new_y > 0 ? (new_y - 40)/2 : 0;
		this.previousArrow.setStyle('margin-top', new_y);
		this.NextArrow.setStyle('margin-top', new_y);
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
		new Fx.Scroll.Carousel('boxarts',{
			mode: 'horizontal',
			loopOnScrollEnd: true
		});

		// Track
		this.boxarts.include(id);
	},

	removeBoxart: function(id){
		$('boxart_'+id).nix({duration: 1000, onComplete:function(){this.centralizeArrows();}.bind(this)}, true);
		this.boxarts.erase(id);
	}
});
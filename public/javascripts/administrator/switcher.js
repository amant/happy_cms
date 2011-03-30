/**
 * Switcher behavior for configuration component
**/
var Switcher = new Class({
	initialize: function(toggler, element)
	{
		var self = this;

		togglers = $ES('a', toggler);
		for (i=0; i < togglers.length; i++) {
			togglers[i].addEvent( 'click', function() { self.switchTo(this.getAttribute('id')); } );
		}

		//hide all
		elements = $ES('div', element);
		for (i=0; i < elements.length; i++) {
			this.hide(elements[i])
		}
	},

	switchTo: function(id)
	{
		toggler = $(id);
		element = $('page-'+id);

		if(element)
		{
			//hide old element
			if(this.active) {
				this.hide(this.active);
			}

			//show new element
			this.show(element);

			toggler.addClass('active');
			if (this.test) {
				$(this.test).removeClass('active');
			}
			this.active = element;
			this.test = id;
		}
	},

	hide: function(element) {
		element.setStyle('display', 'none');
	},

	show: function (element) {
		element.setStyle('display', 'block');
	}
});

document.switcher = null;
Window.onDomReady(function(){
 	toggler = $('submenu')
  	element = $('config-document')
  	if(element) {
  		document.switcher = new Switcher(toggler, element)
  	 	document.switcher.switchTo('site');
  	}
});
(function($){

	function DT_media ( $el, title ) {
		var object = this;

		this.$el = $el || null;
		this.title = title || 'Choose Image';

		//Extend the wp.media object
		this.custom_uploader = wp.media({
			title: this.title,
			button: {
				text: this.title
			},
			multiple: false,
			sidebar: false,
			library: {
				type: this.$el.data( 'type' )
			}

		});

		this.$el.find('.file-add').click(function(e) {
			//Open the uploader dialog
			object.custom_uploader.open();
			e.preventDefault();
		});

		this.$el.find('.file-remove').click(function (e) {
			e.preventDefault();
			object.remove();
		});

		//When a file is selected, grab the URL and set it as the text field's value
		this.custom_uploader.on('select', function() {
			var attachment = object.custom_uploader.state().get('selection').first().attributes;
			$('.file-remove').removeClass('hidden');
			object.add( attachment );
		});
	}

	DT_media.prototype.add = function ( attachment ) {
		var icon;
		if ( attachment.sizes ) {
			if( attachment.sizes.medium )
				icon = attachment.sizes.medium.url;
			else
				icon = attachment.sizes.full.url;
		} else {
			icon = attachment.icon;
		}

		this.$el.find('.file-name').html(attachment.filename);
		this.$el.find('.file-icon').attr('src', icon);
		this.$el.find('.file-value').val(attachment.id);

		this.$el.find('.file-missing').addClass('hidden');
		this.$el.find('.file-exists').removeClass('hidden');
	};

	DT_media.prototype.remove = function () {
		this.$el.find('.file-value').val('').trigger('change');
		this.$el.find('.file-icon').attr('src', '');
		this.$el.find('.file-name').text('');

		this.$el.find('.file-missing').removeClass('hidden');
		this.$el.find('.file-exists').addClass('hidden');
	};

	function change_input_names( element, remove_old_values, old_index, new_index ) {
		element.find('label').each(function(){
			var attrFor = $(this).attr('for');
			$(this).attr('for', attrFor.replace(old_index, new_index ) );
		});
		element.find('button').each(function(){
			var attrFor = $(this).attr('id');
			$(this).attr('id', attrFor.replace(old_index, new_index ) ).attr( 'name', attrFor.replace(old_index, new_index ) );
		});
		element.find('input').each(function(){
			var attrFor = $(this).attr('id');
			$(this).attr('id', attrFor.replace(old_index, new_index ) );
			attrFor = $(this).attr('name');
			$(this).attr('name', attrFor.replace(old_index, new_index ) );

			if( remove_old_values )
				$(this).val( '' );
		});
		element.find('select').each(function(){
			var attrFor = $(this).attr('id');
			$(this).attr('id', attrFor.replace(old_index, new_index ) );
			attrFor = $(this).attr('name');
			$(this).attr('name', attrFor.replace(old_index, new_index ) );

			if( remove_old_values )
				$(this).children('option').eq(0).attr('selected','selected');
		});
		element.find('textarea').each(function(){
			var attrFor = $(this).attr('id');
			$(this).attr('id', attrFor.replace(old_index, new_index ) );
			attrFor = $(this).attr('name');
			$(this).attr('name', attrFor.replace(old_index, new_index ) );

			if( remove_old_values )
				$(this).val( '' );
		});

		return element;
	}

	function DT_duplicator( context, element_selector, add_selector, remove_selector, extra_callbacks ) {
		this.element_selector = element_selector;
		this.add_selector = add_selector;
		this.remove_selector = remove_selector;
		this.extra = extra_callbacks;

		this.add = function(button) {
			var elements = $( element_selector, context);
			var new_index = elements.length + 1;
			var new_element = elements.eq(0).clone();
			var element_class = element_selector.replace('.','');
			new_element.removeClass( element_class + '-1' ).addClass( element_class + '-' + new_index ).removeClass('hidden');
			new_element = change_input_names( new_element, true, '1', new_index );

			var duplicator = this;

			if( extra_callbacks['add'] )
				extra_callbacks['add'](new_element, context );

			new_element.find( remove_selector ).click( function(e) {
				duplicator.remove($(this));
				e.preventDefault();
			});

			button.before(new_element);
		};

		this.init = function() {

			var duplicator = this;
			$( add_selector, context ).click( function(e) {
				duplicator.add($(this));
				e.preventDefault();
			});

			$( remove_selector, context ).click( function(e) {
				duplicator.remove($(this));
				e.preventDefault();
			});

		};


		this.remove = function(button) {
			button.parents(element_selector).addClass('hidden');
			button.parents(element_selector).find('input').val('');
			button.parents(element_selector).find('textarea').val('');
			button.parents(element_selector).find('select').each(function(){
				$(this).children('option').eq(0).attr('selected','selected');
			});
			if( extra_callbacks['remove'] )
				extra_callbacks['remove']( button.parents(element_selector ), context );
		};
	}

	$(document).ready(function(){
		var images = [];

		$('.options-page-image').each( function (e) {
			images[e] = new DT_media( $(this), 'Choose an image' );
		});
		$('.custom-icon').each( function (e) {
			images[e] = new DT_media( $(this), 'Choose an image' );
		});

		var icon_add = function ( new_icon, context ) {
			new_icon.each( function (e) {
				images[e] = new DT_media( $(this), 'Choose an image' );
			});
			new_icon.find('img').attr('src','');
			new_icon.find('.file-missing').removeClass('hidden');

			$('.custom-icons-count',context).val( $('.custom-icon', context).length + 1 );
		};

		var icon_remove = function ( icon, context ) {
			var index = icon.index()-1;
			icon.remove();
			var other_icons = $('.custom-icon', context);
			var total = other_icons.length;

			other_icons.slice(index, total ).each(function(){
				index = $(this).index() + 1;
				change_input_names($(this), false, index.toString(), (index-1).toString());
			});

			$('.custom-icons-count',context).val( total );

		};

		$('.custom-icons-count').each( function() {
			var context = $(this).parent();
			var icon_duplicator = new DT_duplicator( context, '.custom-icon', '.add-custom-icon', '.file-remove', {'add': icon_add, 'remove': icon_remove } );
			icon_duplicator.init();
		});

		$('.file-remove').click( function(e) {
			var parent = $(this).parents('td');
			$('.file',parent).val('');
			$('.has-file',parent).hide();
			$('.add-file',parent).show();
			e.preventDefault();
		} );

	});

})(jQuery);

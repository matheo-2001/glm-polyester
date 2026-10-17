/* Sélecteur de photos dans les fiches Modèle et Réalisation. */
jQuery(function ($) {
	$('.glm-picker').each(function () {
		var $picker = $(this);
		var $input = $picker.find('input[type="hidden"]');
		var $preview = $picker.find('.glm-picker__preview');
		var multiple = $picker.data('multiple') === 1 || $picker.data('multiple') === '1';
		var frame;

		$picker.on('click', '.glm-picker__choose', function (event) {
			event.preventDefault();
			if (!frame) {
				frame = wp.media({
					title: multiple ? 'Choisir les photos' : 'Choisir la photo',
					library: { type: 'image' },
					multiple: multiple ? 'add' : false,
					button: { text: 'Utiliser' }
				});
				frame.on('select', function () {
					var ids = [];
					$preview.empty();
					frame.state().get('selection').each(function (attachment) {
						var data = attachment.toJSON();
						var src = data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
						ids.push(data.id);
						$('<img alt="">').attr('src', src).css({ width: 90, height: 90, objectFit: 'cover' }).appendTo($preview);
					});
					$input.val(ids.join(','));
				});
			}
			frame.open();
		});

		$picker.on('click', '.glm-picker__clear', function (event) {
			event.preventDefault();
			$input.val('');
			$preview.empty();
		});
	});
});

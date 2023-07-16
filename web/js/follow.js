$(document).ready(() => {
	$.fn.follow = function() {
		return this.each((i, el) => {
			el = $(el);
			let isMove = false;

			el.on('mouseenter', () => {
				if (isMove) return;
				isMove = true;
				$(document).on('mousemove', (event) => {
					el.css({
						top: event.clientY - el.height()/2 + 'px',
						left: event.clientX - el.width()/2 + 'px'
					});
				});
				el.css('position', 'fixed').css('cursor', 'move');
			});

			$(document).on('mouseup', () => {
				$(document).off('mousemove');
				isMove = false;
			});
		});
	};
});
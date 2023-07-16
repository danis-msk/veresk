$(document).ready(() => {
	const resizeForm = $('.resize-form');
	const debugInfoContainer = $('.debug-info-container');
	const spinner = $('.spinner');
  
	resizeForm.on('submit', async (event) => {
		event.preventDefault();
	
		const formData = new FormData();
		formData.append('width', $('#width-input').val());
		formData.append('height', $('#height-input').val());
		spinner.addClass('spinner-visible');
	
		try {
			const csrfToken = yii.getCsrfToken();
			const response = await fetch('/image/resize-and-watermark', {
				method: 'POST',
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'X-CSRF-Token': csrfToken,
				},
				body: formData,
			});
			if (response.ok) {
				const data = await response.text();
				debugInfoContainer.html(data);
				alert('Процесс успешно выполнен');
			} else {
				throw new Error('Произошла ошибка');
			}
		} catch (error) {
			console.error('Произошла ошибка:', error);
			alert('Произошла ошибка');
		} finally {
			spinner.removeClass('spinner-visible');
		}
	});

	$('.follow-element').follow();
});
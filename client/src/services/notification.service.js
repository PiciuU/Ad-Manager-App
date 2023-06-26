const NotificationService = {
	displayError(title = 'Ups... Coś poszło nie tak.', message = 'Wystąpił nieoczekiwany błąd. Prosimy odswieżyć stronę lub spróbować ponownie później.', duration = 3000) {
		ElNotification({
			type: 'error',
			title: title,
			message: message,
			duration: duration
		});
	},

	displaySuccess(title = 'Sukces!', message = 'Pomyślnie wykonano żądaną operację.', duration = 3000) {
		ElNotification({
			type: 'success',
			title: title,
			message: message,
			duration: duration
		});
	},

	displayMessage(type = 'warning', message = 'Wystąpił nieoczekiwany błąd. Prosimy odswieżyć stronę lub spróbować ponownie później.', duration = 3000) {
		return ElMessage({
			showClose: true,
			message: message,
			type: type,
			center: true,
			duration: duration
		})
	},

	displayConfirmation(message = 'Czy na pewno chcesz wykonać tą akcję?') {
		return ElMessageBox.confirm(message)
			.then(() => true)
			.catch(() => false);
	}
};

export default NotificationService;
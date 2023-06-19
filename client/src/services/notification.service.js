import 'element-plus/es/components/notification/style/css';
import { ElNotification } from 'element-plus';

const NotificationService = {
	displayError(title = 'Ups... Coś poszło nie tak.', message = 'Wystąpił nieoczekiwany błąd. Prosimy odswieżyć stronę lub spróbować ponownie później.') {
		ElNotification({
			type: 'error',
			title: title,
			message: message,
			duration: 3000
		});
	},

	displaySuccess(title = 'Sukces!', message = 'Pomyślnie wykonano żądaną operację.') {
		ElNotification({
			type: 'success',
			title: title,
			message: message,
			duration: 3000
		});
	},
};

export default NotificationService;
import 'element-plus/es/components/notification/style/css';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/message-box/style/css';
import { ElNotification, ElMessage, ElMessageBox } from 'element-plus';

const NotificationService = {
	displayError(title = 'Ups... Coś poszło nie tak.', message = 'Wystąpił nieoczekiwany błąd. Prosimy odswieżyć stronę lub spróbować ponownie później.', duration = 3000) {
		ElNotification({
			type: 'error',
			title: title,
			message: message,
			duration: duration
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

	displayMessage(type = 'warning', message = 'Wystąpił nieoczekiwany błąd. Prosimy odswieżyć stronę lub spróbować ponownie później.') {
		return ElMessage({
			showClose: true,
			message: message,
			type: type,
			center: true
		})
	},

	displayConfirmation(message = 'Czy na pewno chcesz wykonać tą akcję?') {
		return ElMessageBox.confirm(message)
			.then(() => true)
			.catch(() => false);
	}
};

export default NotificationService;
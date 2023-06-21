import 'element-plus/es/components/loading/style/css';
import { ElLoading } from 'element-plus';

const LoaderService = {
	loader: {},
	create() {
		this.loader = ElLoading.service({
			lock: true,
			text: 'Ładowanie aplikacji...',
			background: 'rgba(0, 0, 0, 0.0)'
		});
	},
	destroy() {
		this.loader.close();
		this.loader = {};
	}
};

export default LoaderService;
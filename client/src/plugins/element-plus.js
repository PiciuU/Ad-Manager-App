import 'element-plus/theme-chalk/dark/css-vars.css';

import 'element-plus/es/components/notification/style/css';
import 'element-plus/es/components/message/style/css';
import 'element-plus/es/components/message-box/style/css';

const components = [];

export const elementPlus = {
    install: (app) => {
        components.forEach((component) => {
            app.use(component)
        })
    }
}

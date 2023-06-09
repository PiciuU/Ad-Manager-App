import { ElButton, ElColorPicker } from 'element-plus'

const components = [ElButton, ElColorPicker]

export const elementPlus = {
    install: (app) => {
        components.forEach((component) => {
            app.use(component)
        })
    },
};
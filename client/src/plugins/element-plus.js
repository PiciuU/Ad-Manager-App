import { ElButton, ElColorPicker, ElSkeleton, ElDatePicker, ElSwitch } from 'element-plus'
const components = [ElButton, ElColorPicker, ElSkeleton, ElDatePicker, ElSwitch]

export const elementPlus = {
    install: (app) => {
        components.forEach((component) => {
            app.use(component)
        })
    }
}

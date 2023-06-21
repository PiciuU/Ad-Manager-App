import { ElButton, ElColorPicker, ElSkeleton, ElDatePicker } from 'element-plus'

const components = [ElButton, ElColorPicker, ElSkeleton, ElDatePicker]

export const elementPlus = {
    install: (app) => {
        components.forEach((component) => {
            app.use(component)
        })
    }
}

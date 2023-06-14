import { ElButton, ElColorPicker, ElSkeleton } from 'element-plus'

const components = [ElButton, ElColorPicker, ElSkeleton]

export const elementPlus = {
    install: (app) => {
        components.forEach((component) => {
            app.use(component)
        })
    }
}

import { ElButton, ElColorPicker } from 'element-plus'

const components = [ElButton, ElColorPicker]

export default (app) => {
    components.forEach((component) => {
        app.use(component)
    })
}

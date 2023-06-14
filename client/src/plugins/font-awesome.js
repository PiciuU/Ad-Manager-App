import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faUserSecret } from '@fortawesome/free-solid-svg-icons'
import { faArrowDown } from '@fortawesome/free-solid-svg-icons'
import { faHome } from '@fortawesome/free-solid-svg-icons'
import { faSearch } from '@fortawesome/free-solid-svg-icons/faSearch'
import { faTwitter } from '@fortawesome/free-brands-svg-icons'
import { faSignOutAlt } from '@fortawesome/free-solid-svg-icons/faSignOutAlt'
import { faUser } from '@fortawesome/free-solid-svg-icons/faUser'
import { faBars } from '@fortawesome/free-solid-svg-icons/faBars'
import { faAddressCard } from '@fortawesome/free-solid-svg-icons/faAddressCard'
import { faChartBar } from '@fortawesome/free-solid-svg-icons/faChartBar'
import { faWrench } from '@fortawesome/free-solid-svg-icons'
import { faCircleUser } from '@fortawesome/free-solid-svg-icons'
import { faCompass } from '@fortawesome/free-solid-svg-icons/faCompass'
import { faQuestion } from '@fortawesome/free-solid-svg-icons'
import { faLock } from '@fortawesome/free-solid-svg-icons/faLock'
import { faKey } from '@fortawesome/free-solid-svg-icons/faKey'
import { faEnvelope } from '@fortawesome/free-solid-svg-icons/faEnvelope'
import { faChartLine } from '@fortawesome/free-solid-svg-icons/faChartLine'
import { faChartArea } from '@fortawesome/free-solid-svg-icons/faChartArea'
import { faEye } from '@fortawesome/free-solid-svg-icons/faEye'
import { faMouse } from '@fortawesome/free-solid-svg-icons/faMouse'

library.add(
    faChartLine,
    faChartArea,
    faMouse,
    faEye,
    faUserSecret,
    faWrench,
    faAddressCard,
    faBars,
    faChartBar,
    faQuestion,
    faUser,
    faSignOutAlt,
    faCircleUser,
    faCompass,
    faSearch,
    faChartBar,
    faTwitter,
    faArrowDown,
    faHome,
    faLock,
    faKey,
    faEnvelope
)

export const fontAwesome = {
    install: (app) => {
        app.component('font-awesome-icon', FontAwesomeIcon)
    }
}

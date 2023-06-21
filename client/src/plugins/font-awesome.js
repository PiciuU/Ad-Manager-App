import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'


import { faTwitter } from '@fortawesome/free-brands-svg-icons'

import { faUserSecret, faArrowDown, faHome, faSearch, faSignOutAlt, faUser, faBars, faAddressCard, faChartBar, faWrench, faCircleUser, faCompass, faQuestion, faLock, faKey, faEnvelope, faChartLine, faChartArea, faEye, faMouse, faCog, faUsers, faRectangleAd, faBook, faUserShield }  from '@fortawesome/free-solid-svg-icons';


library.add(
    faChartLine,
    faCog,
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
    faEnvelope,
    faUsers,
    faRectangleAd,
    faBook,
    faUserShield
)

export const fontAwesome = {
    install: (app) => {
        app.component('font-awesome-icon', FontAwesomeIcon)
    }
}

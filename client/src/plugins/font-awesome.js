import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faUserSecret } from '@fortawesome/free-solid-svg-icons'
import { faArrowDown } from '@fortawesome/free-solid-svg-icons'
import { faHome } from '@fortawesome/free-solid-svg-icons'
import { faSearch } from '@fortawesome/free-solid-svg-icons/faSearch'
import { faTwitter } from '@fortawesome/free-brands-svg-icons'
library.add(faUserSecret, faSearch, faTwitter, faArrowDown, faHome)

export default (app) => {
  app.component('font-awesome-icon', FontAwesomeIcon)
}

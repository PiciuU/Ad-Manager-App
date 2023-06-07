const ADVERT_STATUSES = {
    active: 'Aktywna',
    inactive: 'Nieaktywna'
}

const ADVERT_TYPES = {
    top: 'Baner poziomy - "Billboard"',
    ad200: 'Kwadrat na stronie głównej',
    ad: 'Ogłoszenie na stronie głównej',
    v_right: 'Baner pionowy - "Duży wieżowiec"'
}

export function isStringEmpty(string) {
    return !string || string.trim().length === 0
}

export function isObjectEmpty(obj) {
    return Object.keys(obj).length === 0 || obj.constructor !== Object
}

export function stringToLocale(value) {
    if (typeof value !== 'string' || !value || value.length === 0) return 'Brak danych'
    return value
}

export function truncateString(string = '', maxLength = 20) {
    return string.length > maxLength ? string.substring(0, maxLength) + '...' : string
}

export function advertStatusToLocaleString(status) {
    const key = Object.keys(ADVERT_STATUSES).find((key) => key === status)
    return stringToLocale(ADVERT_STATUSES[key])
}

export function advertTypeToLocaleString(type) {
    const key = Object.keys(ADVERT_TYPES).find((key) => key === type)
    return stringToLocale(ADVERT_TYPES[key])
}

export default {
    advertStatusToLocaleString,
    advertTypeToLocaleString,
    stringToLocale,
    isStringEmpty,
    isObjectEmpty,
    truncateString
}

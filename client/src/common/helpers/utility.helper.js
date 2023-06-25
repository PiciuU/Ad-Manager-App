const ADVERT_STATUSES = {
    active: 'Aktywna',
    inactive: 'Nieaktywna',
    unpaid: 'Nieopłacona',
    expired: 'Wygasła'
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

export default {
    advertStatusToLocaleString,
    stringToLocale,
    isStringEmpty,
    isObjectEmpty,
    truncateString
}

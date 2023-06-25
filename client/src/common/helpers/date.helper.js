import { format, startOfWeek, subMonths, isSameDay } from 'date-fns';

const getMonth = (date) => format(new Date(date), 'yyyy-MM');

export const getCurrentWeek = () => format(startOfWeek(new Date()), 'yyyy-MM-dd');
export const getCurrentMonth = () => format(new Date(), 'yyyy-MM');
export const getCurrentYear = () => format(new Date(), 'yyyy');
export const getCurrentMonthRange = (amount = 11) => [ getMonth(subMonths(new Date(), amount)), getCurrentMonth() ];
export const isToday = (date) => isSameDay(date, new Date());
export const convertToDateFormat = (date, dateFormat) => format(date, dateFormat);

export default { getCurrentWeek, getCurrentMonth, getCurrentYear, getCurrentMonthRange, isToday, convertToDateFormat };
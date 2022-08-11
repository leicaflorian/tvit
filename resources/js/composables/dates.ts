import * as dayjs from 'dayjs'
import * as utc from 'dayjs/plugin/utc'
import * as timezone from 'dayjs/plugin/timezone'

dayjs.extend(utc)
dayjs.extend(timezone)

export function formatTime (date: string, setTimezone = true) {
  const userTz = Intl.DateTimeFormat().resolvedOptions().timeZone
  const format = 'HH:mm'
  
  if (setTimezone) {
    const tzDate = date.endsWith('Z') ? date : date + 'Z'
    
    return dayjs(tzDate).tz(userTz).format(format)
  }
  
  return dayjs(date).format('HH:mm')
}

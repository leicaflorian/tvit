export function formatTime (date: string, setTimezone = true) {
  const userTz = Intl.DateTimeFormat().resolvedOptions().timeZone
  const format = 'HH:mm'
  
  if (setTimezone) {
    const tzDate = date.endsWith('Z') ? date : date + 'Z'
    
    return Intl.DateTimeFormat('it', { timeStyle: 'short' }).format(new Date(tzDate))
  }
  
  return Intl.DateTimeFormat('it', { timeStyle: 'short', timeZone: 'utc' }).format(new Date(date))
}

export function formatTime (date: string, setTimezone = true) {
  const userTz = Intl.DateTimeFormat().resolvedOptions().timeZone
  const format = 'HH:mm'
  
  if (setTimezone) {
    const tzDate = date.endsWith('Z') ? date : date + 'Z'
    
    return Intl.DateTimeFormat('it', { timeStyle: 'short' }).format(new Date(tzDate))
  }
  
  return Intl.DateTimeFormat('it', { timeStyle: 'short', timeZone: 'utc' }).format(new Date(date))
}

export function toUtcDate (date: string) {
  return new Date(date.endsWith('Z') ? date : date + 'Z')
}

/**
 * Percentage (0-100) of a program already aired at the given time.
 */
export function programProgress (program: { start_tz: string, end_tz: string } | null, now = Date.now()) {
  if (!program?.start_tz || !program?.end_tz) {
    return 0
  }

  const start = toUtcDate(program.start_tz).getTime()
  const end = toUtcDate(program.end_tz).getTime()

  return Math.min(100, Math.max(0, (now - start) / (end - start) * 100))
}

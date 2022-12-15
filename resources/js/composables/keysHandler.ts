import { useChannelsStore } from '../store/channels.js'

export function useKeysHandler () {
  const channelStore = useChannelsStore()
  
  window.addEventListener('keyup', (e) => {
    const keysToPrevent = ['ArrowUp', 'ArrowDown', 'Enter']
    
    if (keysToPrevent.includes(e.key)) {
      e.preventDefault()
      e.stopPropagation()
    }
    
    switch (e.key) {
      case 'ArrowUp':
        channelStore.setHighlightedChannel(-1)
        break
      case 'ArrowDown':
        channelStore.setHighlightedChannel(1)
        break
      case 'Enter':
        channelStore.setChannelPlaying(channelStore.highlightedChannel)
        break
    }
  })
}

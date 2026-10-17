import { useChannelsStore } from '../store/channels.js'

export function useKeysHandler () {
  const channelStore = useChannelsStore()
  
  window.addEventListener('keyup', (e) => {
    const keysToPrevent = ['ArrowUp', 'ArrowDown', 'Enter', 'PageUp', 'PageDown', 'ChannelUp', 'ChannelDown']
    
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
      case 'PageUp':
      case 'ChannelUp':
        channelStore.zapChannel(-1)
        break
      case 'PageDown':
      case 'ChannelDown':
        channelStore.zapChannel(1)
        break
    }
  })
}

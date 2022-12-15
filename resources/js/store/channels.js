import { defineStore } from 'pinia'

export const useChannelsStore = defineStore('channels', {
  state: () => ({
    list: [],
    filterGroup: '',
    channelPlaying: null,
    activeChannel: null,
    highlightedChannel: null,
    // use index to get highlighted channel as a getter
    highlightedChannelIndex: null,
  }),
  getters: {
    groups (state) {
      const toReturn = {}
      
      state.list.forEach(element => {
        if (!toReturn[element.groupTitle]) {
          toReturn[element.groupTitle] = ''
        }
      })
      
      return Object.keys(toReturn)
    },
    groupedChannels (state) {
      const groups = {}
      const filteredChannels = state.list.filter((item) => {
        if (state.filterGroup) {
          return item.groupTitle === state.filterGroup
        }
        return true
      })
      
      filteredChannels.forEach(element => {
        if (!groups[element.groupTitle]) {
          groups[element.groupTitle] = []
        }
        
        groups[element.groupTitle].push(element)
      })
      
      return groups
    }
  },
  actions: {
    filterBy (group) {
      this.filterGroup = group
      window.location.href = '#' + group
    },
    setChannelPlaying (channel) {
      this.channelPlaying = channel
    },
    setActiveChannel (channel) {
      this.activeChannel = channel
    },
    setHighlightedChannel (increment, channel = null) {
      const currentIndex = this.list.findIndex((item) => item.id === this.highlightedChannel.id)
      
      if(channel) {
        this.highlightedChannel = channel
        return
      }
      
      if (currentIndex === 0 && increment === -1) {
        this.highlightedChannel = this.list[this.list.length - 1]
      } else if (currentIndex === this.list.length - 1 && increment === 1) {
        this.highlightedChannel = this.list[0]
      } else {
        this.highlightedChannel = this.list[currentIndex + increment]
      }
    }
  }
})

import { defineStore } from 'pinia'

export const useChannelsStore = defineStore('channels', {
  state: () => ({
    list: [],
    filterGroup: '',
    channelPlaying: null
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
    }
  }
})

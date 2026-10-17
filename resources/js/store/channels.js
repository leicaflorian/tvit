import { defineStore } from 'pinia'

export const useChannelsStore = defineStore('channels', {
  state: () => ({
    list: [],
    filterGroup: '',
    search: '',
    channelPlaying: null,
    activeChannel: null,
    highlightedChannel: null,
    // use index to get highlighted channel as a getter
    highlightedChannelIndex: null,
  }),
  getters: {
    groups (state) {
      return [...new Set(state.list.map(channel => channel.group))]
    },
    filteredList (state) {
      const search = state.search.trim().toLowerCase()

      return state.list.filter((channel) => {
        if (state.filterGroup && channel.group !== state.filterGroup) {
          return false
        }

        if (!search) {
          return true
        }

        return channel.name.toLowerCase().includes(search)
          || String(channel.dtt_num) === search
          || channel.now_on_air?.title?.toLowerCase().includes(search)
      })
    }
  },
  actions: {
    filterBy (group) {
      this.filterGroup = group
      this.highlightFirstIfHidden()
    },
    searchBy (text) {
      this.search = text
      this.highlightFirstIfHidden()
    },
    highlightFirstIfHidden () {
      const list = this.filteredList

      if (list.length && !list.some(channel => channel.id === this.highlightedChannel?.id)) {
        this.highlightedChannel = list[0]
      }
    },
    setChannelPlaying (channel) {
      this.channelPlaying = channel
    },
    // Switch the playing channel to the previous/next one, like a remote control
    zapChannel (increment) {
      if (!this.channelPlaying) {
        return
      }

      const filtered = this.filteredList
      const list = filtered.some(item => item.id === this.channelPlaying.id) ? filtered : this.list
      const currentIndex = list.findIndex(item => item.id === this.channelPlaying.id)

      if (currentIndex === -1 || list.length < 2) {
        return
      }

      const next = list[(currentIndex + increment + list.length) % list.length]

      this.channelPlaying = next
      this.highlightedChannel = next
    },
    setActiveChannel (channel) {
      this.activeChannel = channel
    },
    setHighlightedChannel (increment, channel = null) {
      if (channel) {
        this.highlightedChannel = channel
        return
      }

      const list = this.filteredList

      if (!list.length) {
        return
      }

      const currentIndex = list.findIndex((item) => item.id === this.highlightedChannel?.id)
      // wrap around both ends of the visible list
      const nextIndex = currentIndex === -1 ? 0 : (currentIndex + increment + list.length) % list.length

      this.highlightedChannel = list[nextIndex]
    }
  }
})

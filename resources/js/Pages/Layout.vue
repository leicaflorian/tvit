<template>
  <TheNavbar></TheNavbar>

  <main :class="{'playing': channelsStore.channelPlaying}">
    <slot/>
  </main>

  <ThePlayer></ThePlayer>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import ThePlayer from '../Components/ThePlayer.vue'
import TheNavbar from '../Components/TheNavbar.vue'
import { useChannelsStore } from '../store/channels'
import { onBeforeMount } from 'vue'

export default {
  components: {
    Link,
    ThePlayer,
    TheNavbar
  },
  props: () => this.$attrs,
  setup (props) {
    const channelsStore = useChannelsStore()

    return {
      channelsStore
    }
  }
}
</script>

<style scoped lang="scss">
main {
  transition: margin-bottom .3s;
}


.playing {
  margin-bottom: 60px;
}

</style>

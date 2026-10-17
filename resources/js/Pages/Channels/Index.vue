<template>
  <Head title="Canali"/>

  <div class="main-container">
    <div class="left-channels-list overflow-auto">
      <SingleChannelCard v-for="channel in channels" :key="channel.id"
                         :channel="channel"
                         :active="channelsStore.highlightedChannel.id === channel.id"/>
    </div>

    <div class="right-channels-preview">
      <ChannelPreview v-if="channelsStore.highlightedChannel"/>
    </div>
  </div>

</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import { useChannelsStore } from '../../store/channels'
import { onMounted, onBeforeMount } from 'vue'
import SingleChannelCard from '../../Components/SingleChannelCard.vue'
import ChannelPreview from '../../Components/ChannelPreview.vue'

export default {
  components: {
    ChannelPreview,
    SingleChannelCard,
    Head
  },
  props: {
    channels: Array
  },
  setup (props) {
    const channelsStore = useChannelsStore()

    onBeforeMount(async () => {
      channelsStore.$patch({
        list: props.channels,
        highlightedChannel: props.channels[0]
      })

      channelsStore.setActiveChannel(props.channels[0])
    })

    return {
      channelsStore
    }
  }
}
</script>

<style scoped lang="scss">
@import '../../../scss/partials/bsMixins';

.container-fluid {
  min-height: 100%;
  display: flex;
}

.main-container {
  height: 100%;
  display: flex;
}

.left-channels-list {
  flex: 0;
  display: flex;
  flex-direction: column;
  overflow: auto;
  flex-basis: clamp(320px, 30vw, 460px);
  padding: 1rem 1.25rem;
  gap: 1rem;
}

.right-channels-preview {
  flex: 1;
  min-width: 0;
}

@include media-breakpoint-down("md") {
  .main-container {
    flex-direction: column;
  }

  // On mobile the preview goes on top, compact, and the list scrolls below it
  .right-channels-preview {
    order: -1;
    flex: 0 0 auto;
  }

  .left-channels-list {
    flex: 1 1 0;
    min-height: 0;
    padding: 1rem;
  }
}
</style>

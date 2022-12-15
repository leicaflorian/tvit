<template>
  <Head title="Canali"/>

  <div class="main-container">
    <div class="left-channels-list h-100 overflow-auto">
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
  min-width: 400px;
  max-width: 50vh;
  flex-basis: 100%;
  padding: 1rem;
  gap: 1rem;
}

.right-channels-preview {
  flex: 1;
}


@include media-breakpoint-down("lg") {
  .left-channels-list {
    min-width: 300px;
  }
}

@include media-breakpoint-down("md") {
  .main-container {
    flex-direction: column;
  }

  .left-channels-list {
    min-width: 100%;
    max-width: 100%;
  }
}
</style>

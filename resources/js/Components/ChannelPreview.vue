<template>
  <div :style="`background-image: url(${bgImage})`" class="channel-preview">

    <div class="content-container">
      <div class="text-light d-flex align-items-center justify-content-between">

        Ora in onda
        <ProgramsOffCanvas :channel="channel"/>
      </div>

      <h2 class="mb-md-4">{{ channel.now_on_air?.title }}</h2>

      <p class="lead text-light mb-md-5">{{ channel.now_on_air?.description }}</p>

      <ChannelProgress :channel="channel" class="mb-md-3"/>

      <div class="position-static">
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { computed, ComputedRef, defineComponent } from 'vue'
import { useChannelsStore } from '../store/channels.js'
import { Channel } from '../@types/Channel'
import ChannelProgress from './ChannelProgress.vue'
import ProgramsOffCanvas from './ProgramsOffCanvas.vue'

export default defineComponent({
  name: 'ChannelPreview',
  components: { ProgramsOffCanvas, ChannelProgress },
  setup (props) {
    const channelsStore = useChannelsStore()

    const channel: ComputedRef<Channel> = computed(() => channelsStore.highlightedChannel)
    const bgImage = computed(() => channel.value?.now_on_air?.cover_img ?? '/assets/video-placeholder.jpeg')

    return {
      channel,
      bgImage,
      channelsStore
    }
  }
})
</script>

<style scoped lang="scss">
@import '../../scss/partials/bsMixins';

.channel-preview {
  height: 100%;
  background-position: center;
  background-size: cover;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: end;
  padding: 3rem;

  &:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgb(0, 0, 0);
    background: radial-gradient(circle, rgba(var(--bs-dark-rgb), .5) 0%, rgba(var(--bs-dark-rgb), 1) 80%);
  }

  & * {
    position: relative;
  }
}

.content-container {
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-width: 1024px;

  .lead {
    overflow: auto;
    max-height: 20vh;
  }
}

@include media-breakpoint-down("lg") {
  .content-container {
  }
}


@include media-breakpoint-down("md") {
  .channel-preview {
    padding: 1.5rem;
  }

  .content-container {
    height: 100%;

    .lead {
      max-height: 100px;
    }
  }
}

@media screen and (max-height: 700px) and (orientation: landscape) {
  .lead {
    //max-height: 40vh;
  }
}
</style>

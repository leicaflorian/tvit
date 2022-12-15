<template>
  <div class="the-player" :class="{'expanded': expanded}"
       v-show="channelPlaying">
    <div class="the-player-header modal-header" v-if="expanded">
      <div class="d-flex align-items-center ms-1 overflow-hidden">
        <img :src="channelPlaying && channelPlaying.logo_url_color" alt="" class="tvg-logo d-none d-md-block"
             style="height: 40px">

        <div class="ms-2 w-100 overflow-hidden">
          <h5 class="modal-title" id="exampleModalToggleLabel">
            {{ channelPlaying && channelPlaying.name }}
            <small class="mb-0 d-none d-md-inline-block">({{ channelPlaying && channelPlaying.group }})</small>
          </h5>

          <span class="mb-0 overflow-hidden w-100" v-if="channelPlaying && channelPlaying.now_on_air">
            <div class="d-none d-md-inline-block">
              <strong>{{ formatTime(channelPlaying.now_on_air.start_tz) }}</strong> -
              <strong>{{ formatTime(channelPlaying.now_on_air.end_tz) }}</strong>
            - </div>
            <div class="text-truncate d-block">
            {{ channelPlaying.now_on_air.title }}
            </div>
          </span>
        </div>
      </div>

      <div class="d-flex">
        <button type="button" class="btn btn-link" @click="expanded = false">
          <i class="mdi mdi-chevron-down text-white fs-3"></i>
        </button>
        <button type="button" class="btn btn-link" @click="closePlayer">
          <i class="mdi mdi-close text-white fs-3"></i>
        </button>
      </div>
    </div>

    <div class="the-player-body">
      <video ref="videoEl" src="" :controls="expanded" @click="!expanded ? (expanded = true) : null"></video>

      <div class="d-flex align-items-center flex-fill px-3 overflow-hidden" v-if="!expanded">
        <img :src="channelPlaying && channelPlaying.logo_url_color" alt="" class="tvg-logo d-none d-md-block">

        <div class="flex-fill overflow-hidden">
          <h5 class="mb-0">{{ channelPlaying && channelPlaying.name }}
            <small class="mb-0 d-none d-md-inline-block">({{ channelPlaying && channelPlaying.group }})</small>
          </h5>
          <div class="mb-0 w-100 overflow-hidden" v-if="channelPlaying && channelPlaying.now_on_air">
            <span class="d-none d-md-inline-block">
              <strong>{{ formatTime(channelPlaying.now_on_air.start_tz) }}</strong> -
              <strong>{{ formatTime(channelPlaying.now_on_air.end_tz) }}</strong>
            - </span>
            <span class="text-truncate d-block">{{ channelPlaying.now_on_air.title }}</span>
          </div>
        </div>

        <div class="d-flex">
          <button type="button" class="btn btn-link" @click="expanded = true">
            <i class="mdi mdi-chevron-up text-white fs-3"></i>
          </button>
          <button type="button" class="btn btn-link" @click="closePlayer">
            <i class="mdi mdi-close text-white fs-3"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onMounted, ref, watch } from 'vue'
import { useChannelsStore } from '../store/channels'
import { storeToRefs } from 'pinia'
import HLS from 'hls.js'
import dashjs from 'dashjs'
import { formatTime } from '../composables/dates'

export default {
  name: 'ThePlayer',
  setup () {
    const videoEl = ref()
    const expanded = ref(false)
    const channelsStore = useChannelsStore()
    const { channelPlaying } = storeToRefs(channelsStore)
    let player = null

    function destroyPlayer () {
      if (player) {
        player.destroy()
        player = null
      }
    }

    function closePlayer () {
      expanded.value = false
      channelsStore.setChannelPlaying(null)

      destroyPlayer()
    }

    function playHls () {
      destroyPlayer()

      if (HLS.isSupported()) {
        player = new HLS()

        // bind them together
        player.attachMedia(videoEl.value)

        // MEDIA_ATTACHED event is fired by hls object once MediaSource is ready
        player.on(HLS.Events.MEDIA_ATTACHED, function () {
          console.log('channelPlaying changed', channelPlaying)
          player.loadSource(channelPlaying.value.m3u8_link)

          player.on(HLS.Events.MANIFEST_PARSED, function (event, data) {
            console.log('manifest loaded, found ' + data.levels.length + ' quality level')

            videoEl.value.play()

            expanded.value = true
          })
        })
      }
    }

    function playDash () {
      destroyPlayer()

      const url = channelPlaying.value.channel.m3u8_link
      player = dashjs.MediaPlayer().create()

      player.initialize(videoEl.value, url, false)
      player.play()
      expanded.value = true
    }

    watch(channelPlaying, (channel) => {
      if (!channel) {
        return
      }

      if (channel.m3u8_link.toString().endsWith('.mpd')) {
        playDash()
      } else {
        playHls()
      }
    })

    return {
      videoEl,
      expanded,
      channelPlaying,
      closePlayer,
      formatTime
    }
  }
}
</script>

<style scoped lang="scss">
@import '../../scss/partials/bsMixins';

.the-player {
  background-color: var(--bs-dark);
  height: 60px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 99;
  box-shadow: 0 -20px 20px 5px rgba(var(--bs-dark-rgb), 1);
  transition: height .3s;

  &.expanded {
    height: 100vh;

    .the-player-header {
      position: absolute;
      width: 100%;
      top: 0;
      left: 0;
      height: 100px;
      background-color: rgb(0, 0, 0);
      background: linear-gradient(0deg, rgba(var(--bs-dark-rgb), 0) 0%, rgba(var(--bs-dark-rgb), 1) 100%);
      z-index: 99;
    }

    .the-player-body {
      flex: 1;
      overflow: hidden;

      video {
        width: 100%;
        height: 100%;
      }
    }
  }

  .the-player-header {
    display: flex;
    justify-content: space-between;

    .tvg-logo {
      filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.5));
    }
  }

  .the-player-body {
    display: flex;
    justify-content: space-between;
    align-content: center;

    video {
      background-color: black;
      height: 60px;
      width: unset;
    }

    .tvg-logo {
      height: 50px;
      width: 60px;
      object-fit: contain;
      margin-right: 1rem;
      filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.5));
    }
  }
}

.text-white {
  stroke: #fff !important;
}


@include media-breakpoint-down("md") {

  .the-player-header {
  }
}
</style>

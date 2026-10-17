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

      <div class="player-controls">
        <ChannelZapper/>
        <button type="button" class="player-ctrl" title="Riduci (Esc)" aria-label="Riduci player" @click="expanded = false">
          <i class="mdi mdi-arrow-collapse"></i>
        </button>
        <button type="button" class="player-ctrl" title="Chiudi" aria-label="Chiudi player" @click="closePlayer">
          <i class="mdi mdi-close"></i>
        </button>
      </div>
    </div>

    <div class="the-player-body">
      <video ref="videoEl" src="" :controls="expanded" @click="!expanded ? (expanded = true) : null"></video>

      <div class="player-error" v-if="expanded && errorMessage">
        <i class="mdi mdi-alert-circle-outline"></i>
        <p>{{ errorMessage }}</p>
        <small v-if="isGeoError">I canali Rai sono trasmessi in streaming solo verso connessioni italiane.</small>
      </div>

      <div class="d-flex align-items-center flex-fill px-3 overflow-hidden" v-if="!expanded">
        <img :src="channelPlaying && channelPlaying.logo_url_color" alt="" class="tvg-logo d-none d-md-block">

        <div class="mini-info flex-fill overflow-hidden">
          <h5 class="mb-0 text-truncate">{{ channelPlaying && channelPlaying.name }}
            <small class="d-none d-md-inline">({{ channelPlaying && channelPlaying.group }})</small>
          </h5>
          <div class="mini-program mini-error" v-if="errorMessage">
            <i class="mdi mdi-alert-circle-outline"></i>
            <span class="text-truncate">{{ errorMessage }}</span>
          </div>
          <div class="mini-program" v-else-if="channelPlaying && channelPlaying.now_on_air">
            <strong class="d-none d-md-inline">
              {{ formatTime(channelPlaying.now_on_air.start_tz) }} - {{ formatTime(channelPlaying.now_on_air.end_tz) }}
            </strong>
            <span class="text-truncate">{{ channelPlaying.now_on_air.title }}</span>
          </div>
        </div>

        <div class="player-controls">
          <ChannelZapper class="d-none d-sm-inline-flex"/>
          <button type="button" class="player-ctrl" title="Espandi" aria-label="Espandi player" @click="expanded = true">
            <i class="mdi mdi-arrow-expand"></i>
          </button>
          <button type="button" class="player-ctrl" title="Chiudi" aria-label="Chiudi player" @click="closePlayer">
            <i class="mdi mdi-close"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useChannelsStore } from '../store/channels'
import { storeToRefs } from 'pinia'
import HLS from 'hls.js'
import dashjs from 'dashjs'
import { formatTime } from '../composables/dates'
import ChannelZapper from './ChannelZapper.vue'

export default {
  name: 'ThePlayer',
  components: { ChannelZapper },
  setup () {
    const videoEl = ref()
    const expanded = ref(false)
    const errorMessage = ref(null)
    const isGeoError = ref(false)
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

    function showError (data) {
      const code = data.response?.code
      // 451 comes from our relinker check; the CDN answers 403 abroad, often without CORS (code 0)
      isGeoError.value = code === 451 || code === 403 || (code === 0 && !!channelPlaying.value?.geo_blocked)
      errorMessage.value = isGeoError.value
        ? 'Questo canale è disponibile solo dall\'Italia'
        : 'Impossibile caricare il canale, riprova più tardi'

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

          player.on(HLS.Events.ERROR, function (event, data) {
            if (data.fatal) {
              showError(data)
            }
          })

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

    function onKeyup (e) {
      if (e.key === 'Escape' && expanded.value) {
        expanded.value = false
      }
    }

    onMounted(() => window.addEventListener('keyup', onKeyup))
    onBeforeUnmount(() => window.removeEventListener('keyup', onKeyup))

    watch(channelPlaying, (channel) => {
      errorMessage.value = null
      isGeoError.value = false

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
      errorMessage,
      isGeoError,
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
  border-top: 1px solid rgba(255, 255, 255, .1);
  box-shadow: 0 -4px 12px rgba(0, 0, 0, .35);
  transition: height .3s;

  &.expanded {
    height: 100dvh;

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
      // 16:9 thumbnail, otherwise the video keeps its 300px default width
      width: 107px;
      flex-shrink: 0;
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

.mini-error {
  align-items: center;
  gap: .3rem;
  color: var(--bs-warning);
}

.player-error {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: .5rem;
  padding: 2rem;
  text-align: center;
  background: rgba(0, 0, 0, .75);
  pointer-events: none;

  .mdi {
    font-size: 3rem;
    color: var(--bs-warning);
  }

  p {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
  }

  small {
    color: rgba(255, 255, 255, .7);
  }
}

// Controls share the outlined 36px style of the CH pill
.player-controls {
  display: flex;
  align-items: center;
  gap: .5rem;
  padding: 0 .75rem;
  flex-shrink: 0;
}

.player-ctrl {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  padding: 0;
  border: 1px solid rgba(255, 255, 255, .35);
  border-radius: 50%;
  background: transparent;
  color: white;
  transition: background .15s, border-color .15s;

  &:hover {
    background: rgba(255, 255, 255, .15);
    border-color: white;
  }

  .mdi {
    font-size: 1.15rem;
    line-height: 1;
  }
}

// Minimized bar is 60px tall: name and "times + title" must stay on one line each
.mini-info {
  min-width: 0;
  line-height: 1.3;

  h5 {
    font-size: 1rem;
  }

  .mini-program {
    display: flex;
    gap: .5rem;
    min-width: 0;
    font-size: .85rem;
    white-space: nowrap;

    strong {
      flex-shrink: 0;
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

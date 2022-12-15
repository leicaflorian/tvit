<template>
  <div class="card overflow-hidden"
       ref="el"
       @click="onClick"
       :class="{'now-playing': nowPlaying, 'active': active }">

    <div class="card-body d-flex">
      <div class="logo-container">
        <img :src="channel.logo_url_color">
      </div>

      <div class="content-container" :class="{'has-actions': nowPlaying || active}">
        <h5 class="mb-0">
          {{ channel.name }}
        </h5>

        <div class="flex-grow-1">
          <div class="channel-program-now" v-if="channel.now_on_air">
            {{ channel.now_on_air.title }}
            <br>
            <strong>
              {{ formatTime(channel.now_on_air.start_tz) }}
              <span class="d-none d-lg-inline-block">
                - {{ formatTime(channel.now_on_air.end_tz) }}
              </span>
            </strong>

          </div>
        </div>

        <div class="d-flex gap-2 actions-container">
          <i class="mdi mdi-play" v-if="nowPlaying"></i>

          <Transition name="fade">
            <button class="btn btn-outline-light btn-sm" v-if="!nowPlaying && active">Play</button>
          </Transition>
        </div>
      </div>
    </div>


  </div>
</template>

<script lang="ts" setup>
import { computed, PropType, Ref, ref, watch } from 'vue'
import { Channel } from '../@types/Channel'
import { formatTime } from '../composables/dates'
import { useChannelsStore } from '../store/channels.js'

const channelsStore = useChannelsStore()

const props = defineProps({
  channel: Object as PropType<Channel>,
  active: Boolean
})
const setChannelPlaying = channelsStore.setChannelPlaying
const el: Ref<HTMLElement> = ref()

const nowPlaying = computed(() => channelsStore.channelPlaying?.id === props.channel.id)

const coverImg = computed(() => {
  return props.channel.now_on_air?.cover_img || props.channel.logo_url_light.replace('width=120', '')
})

const coverImgStyle = computed(() => {
  return {
    aspectRatio: 16 / 6,
    objectFit: !props.channel.now_on_air?.cover_img ? 'contain' : 'cover'
  }
})

const onAirDescription = computed(() => {
  if (!props.channel.now_on_air?.description) {
    return 'Testo di presentazione non disponibile'
  }
  let description = props.channel.now_on_air.description
  const length = description.length
  const maxLen = 200

  if (length > maxLen) {
    description = description.substring(0, maxLen) + '...'
  }

  return description
})

function onClick () {

  if (channelsStore.highlightedChannel?.id === props.channel.id) {
    channelsStore.setChannelPlaying(props.channel)
  } else {
    channelsStore.setHighlightedChannel(null, props.channel)
  }

}

watch(() => props.active, (active) => {
  if (active) {

    const scroller = function () {
      const scrollerEl: HTMLElement = el.value.closest('.left-channels-list')

      return {
        el: scrollerEl,
        viewportHeight: scrollerEl.offsetHeight
      }
    }()

    const elDetails = {
      offset: 20,
      top: el.value.offsetTop,
      rect: el.value.getBoundingClientRect()
    }

    const isVisible = (elDetails.rect.top - elDetails.offset) >= 0 && (elDetails.rect.bottom) <= scroller.viewportHeight

    if (!isVisible) {
      scroller.el.scrollTo({
        top: elDetails.top - elDetails.offset,
        behavior: 'smooth'
      })
    }
  }
})
</script>

<style scoped lang="scss">
@import '../../scss/partials/bsMixins';

.card {
  --bs-card-border-color: transparent;
  background: rgb(255, 255, 255);
  background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.1) 100%);
  backdrop-filter: blur(10px);
  transition: background .2s, transform .2s;
  flex-shrink: 0;

  &:before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: linear-gradient(45deg, #2C2A3A, #4B495A), linear-gradient(45deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.8) 100%);
    background-clip: padding-box, border-box;
    background-origin: border-box;
    border: 1px solid transparent;
    border-radius: var(--bs-card-border-radius);
    opacity: 0;
    z-index: -1;
    transition: opacity .2s;
  }

  &.active {
    background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.20) 100%);
    transform: scale(1.05);

    &:before {
      opacity: 1;
    }
  }

  .card-body {
    --bs-card-spacer-y: .8rem;
    --bs-card-spacer-x: .8rem;
    --bs-card-color: white;
  }
}

.logo-container {
  background-color: #21202E;
  aspect-ratio: 1/1;
  flex-basis: 33%;
  flex-shrink: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: aspect-ratio .3s;

  img {
    max-width: 80%;
    max-height: 80%;
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
}

.content-container {
  flex: 1;
  padding-left: var(--bs-card-spacer-x);
  padding-top: var(--bs-card-spacer-y);
  //padding-bottom: var(--bs-card-spacer-y);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
  transition: padding-bottom .2s;

  &.has-actions {
    padding-bottom: 40px;
  }
}

.now-playing {
  box-shadow: 0 0 10px rgba(255, 255, 255, .7);
}

.img-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;

  .dtt-num {
    background: white;
    color: #000;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 0;
    right: 0;
    font-weight: bold;
    padding-left: .5rem;
    padding-right: .5rem;
  }
}

.channel-program-now {
  //line-height: 30px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  font-size: .8rem;
  color: var(--bs-light);
}

.channel-program-next {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  opacity: .7;
}

.actions-container {
  position: absolute;
  bottom: 0;
}

@include media-breakpoint-down("md") {
  .card {
    &.active {
      transform: scale(1.03);
    }
  }

  .logo-container {
    flex-basis: 15%;
  }
}
</style>

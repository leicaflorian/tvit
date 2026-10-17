<template>
  <div class="channel-row"
       ref="el"
       role="button"
       @click="onClick"
       :class="{'now-playing': nowPlaying, 'active': active }">

    <div class="logo-container">
      <img :src="channel.logo_url_color" :alt="channel.name" loading="lazy">
      <span class="dtt-num" v-if="channel.dtt_num">{{ channel.dtt_num }}</span>
    </div>

    <div class="content-container">
      <div class="d-flex align-items-baseline gap-2">
        <h5 class="channel-name">{{ channel.name }}</h5>

        <small class="channel-time ms-auto" v-if="channel.now_on_air">
          {{ formatTime(channel.now_on_air.start_tz) }} - {{ formatTime(channel.now_on_air.end_tz) }}
        </small>
      </div>

      <div class="channel-program-now">
        {{ channel.now_on_air?.title ?? 'Guida non disponibile' }}
      </div>

      <div class="channel-progress" v-if="channel.now_on_air">
        <div :style="{ width: progress + '%' }"></div>
      </div>
    </div>

    <div class="actions-container">
      <i class="mdi mdi-volume-high" v-if="nowPlaying" title="In riproduzione"></i>

      <Transition name="fade">
        <button class="btn btn-light btn-sm rounded-circle" v-if="!nowPlaying && active" title="Guarda">
          <i class="mdi mdi-play"></i>
        </button>
      </Transition>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed, PropType, Ref, ref, watch } from 'vue'
import { Channel } from '../@types/Channel'
import { formatTime, programProgress, useNow } from '../composables/dates'
import { useChannelsStore } from '../store/channels.js'

const channelsStore = useChannelsStore()

const props = defineProps({
  channel: Object as PropType<Channel>,
  active: Boolean
})
const el: Ref<HTMLElement> = ref()

const nowPlaying = computed(() => channelsStore.channelPlaying?.id === props.channel.id)
const now = useNow()
const progress = computed(() => programProgress(props.channel.now_on_air, now.value))

function onClick () {
  if (channelsStore.highlightedChannel?.id === props.channel.id) {
    channelsStore.setChannelPlaying(props.channel)
  } else {
    channelsStore.setHighlightedChannel(null, props.channel)
  }
}

watch(() => props.active, (active) => {
  if (active) {
    el.value.scrollIntoView({ block: 'nearest', behavior: 'smooth' })
  }
})
</script>

<style scoped lang="scss">
.channel-row {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .5rem;
  flex-shrink: 0;
  border-radius: var(--bs-border-radius-lg);
  border: 1px solid transparent;
  background: rgba(255, 255, 255, .06);
  color: white;
  cursor: pointer;
  scroll-margin: .75rem;
  transition: background .2s, border-color .2s;

  &:hover {
    background: rgba(255, 255, 255, .1);
  }

  &.active {
    background: rgba(255, 255, 255, .16);
    border-color: rgba(255, 255, 255, .5);
  }

  &.now-playing {
    border-color: var(--bs-primary);
  }
}

.logo-container {
  position: relative;
  width: 56px;
  height: 56px;
  flex-shrink: 0;
  border-radius: var(--bs-border-radius);
  background-color: #21202E;
  display: flex;
  justify-content: center;
  align-items: center;

  img {
    max-width: 75%;
    max-height: 75%;
    object-fit: contain;
  }

  .dtt-num {
    position: absolute;
    bottom: 2px;
    right: 2px;
    min-width: 18px;
    height: 16px;
    padding: 0 3px;
    border-radius: 4px;
    background: white;
    color: #000;
    font-size: .65rem;
    font-weight: 700;
    line-height: 16px;
    text-align: center;
  }
}

.content-container {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: .2rem;
}

.channel-name {
  margin: 0;
  font-size: 1rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.channel-time {
  flex-shrink: 0;
  font-size: .7rem;
  color: rgba(255, 255, 255, .6);
  font-variant-numeric: tabular-nums;
}

.channel-program-now {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  font-size: .8rem;
  color: rgba(255, 255, 255, .75);
}

.channel-progress {
  height: 3px;
  border-radius: 2px;
  background: rgba(255, 255, 255, .15);
  overflow: hidden;

  div {
    height: 100%;
    background: white;
  }
}

.actions-container {
  width: 32px;
  flex-shrink: 0;
  display: flex;
  justify-content: center;

  .btn {
    width: 32px;
    height: 32px;
    padding: 0;
  }

  .mdi-volume-high {
    font-size: 1.25rem;
    color: var(--bs-primary);
  }
}
</style>

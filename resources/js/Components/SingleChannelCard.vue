<template>
  <div class="card overflow-hidden h-100"
       :class="{'now-playing': channelsStore.channelPlaying?.id === channel.id }">
    <div class="position-relative">
      <img :src="coverImg" class="card-img-top border-bottom"
           :style="coverImgStyle">

      <div class="img-overlay">
        <div class="dtt-num">#{{ channel.dtt_num }}</div>
      </div>
    </div>

    <div class="card-body d-flex flex-column">
      <h5 class="mb-0 d-flex  align-items-center gap-3">
        <img class="" style="height: 30px" :src="channel.logo_url_light">
        {{ channel.name }}
      </h5>

      <hr>

      <div class="flex-grow-1">
        <div class="card-title">
          <div class="channel-program-now" v-if="channel.now_on_air">
            <strong>{{ formatTime(channel.now_on_air.start_tz) }} <span
                class="d-none d-lg-inline-block">- {{ formatTime(channel.now_on_air.end_tz) }}</span></strong> |
            {{ channel.now_on_air.title }}
          </div>
        </div>

        <p class="card-text" v-if="channel.now_on_air" v-html="onAirDescription"></p>
      </div>

      <hr>

      <div class="channel-program-next" v-if="channel.next_on_air">
        <strong>{{ formatTime(channel.next_on_air.start_tz) }} <span
            class="d-none d-lg-inline-block">- {{ formatTime(channel.next_on_air.end_tz) }}</span></strong>
        | {{ channel.next_on_air.title }}
      </div>
    </div>

    <div class="card-footer p-0">
      <div class="btn-group w-100" role="group">
        <button class="btn btn-secondary flex-1 rounded-0" @click="setChannelPlaying(channel)">
          <i class="mdi mdi-play me-2"></i>
          Play
        </button>

        <Link class="btn btn-link  flex-1" :href="`/channels/${channel.tvg_slug}`">
          <i class="mdi mdi-format-list-text me-2"></i>
          Programmi
        </Link>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { Link } from '@inertiajs/inertia-vue3'
import { computed, PropType } from 'vue'
import { Channel } from '../@types/Channel'
import { formatTime } from '../composables/dates'
import { useChannelsStore } from '../store/channels'

const channelsStore = useChannelsStore()

const props = defineProps({
  channel: Object as PropType<Channel>
})
const setChannelPlaying = channelsStore.setChannelPlaying

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
    return ''
  }
  let description = props.channel.now_on_air.description
  const length = description.length
  const maxLen = 200

  if (length > maxLen) {
    description = description.substring(0, maxLen) + '...'
  }

  return description
})
</script>

<style scoped lang="scss">
.now-playing {
  box-shadow: 0 0 10px rgba(var(--bs-primary-rgb), .7);
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
  line-height: 30px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;

  &:before {
    content: "";
    background-image: url("/assets/live_circle.gif");
    width: 30px;
    height: 30px;
    background-size: contain;
    background-position: center;
    display: inline-block;
    background-repeat: no-repeat;
    position: absolute;
    left: -15px;
  }
}

.channel-program-next {
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  opacity: .7;
}
</style>

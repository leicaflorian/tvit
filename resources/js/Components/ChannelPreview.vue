<template>
  <div class="channel-preview">
    <div class="cover" :style="bgImage ? `background-image: url(${bgImage})` : ''"
         :class="{'no-cover': !bgImage}"></div>

    <div class="content-container">
      <div class="channel-info">
        <img :src="channel.logo_url_color" :alt="channel.name">
        <span>{{ channel.name }}</span>
        <span class="dtt-num" v-if="channel.dtt_num">CH {{ channel.dtt_num }}</span>
      </div>

      <template v-if="program">
        <div class="program-meta">
          <span class="badge-live">In onda</span>
          <span v-if="program.category">{{ program.category }}</span>
          <span v-if="program.duration">{{ program.duration }} min</span>
        </div>

        <h2 class="program-title">{{ program.title }}</h2>

        <p class="program-description" v-if="program.description">{{ program.description }}</p>

        <ChannelProgress :channel="channel"/>
        <div class="remaining" v-if="remainingMinutes !== null">
          {{ remainingMinutes > 0 ? `Mancano ${remainingMinutes} min` : 'In chiusura' }}
        </div>
      </template>

      <template v-else>
        <h2 class="program-title">{{ channel.name }}</h2>
        <p class="program-description">Guida programmi non disponibile per questo canale</p>
      </template>

      <div class="actions">
        <button type="button" class="btn btn-light btn-watch" @click="watch" :disabled="isPlaying">
          <i class="mdi" :class="isPlaying ? 'mdi-volume-high' : 'mdi-play'"></i>
          {{ isPlaying ? 'In riproduzione' : 'Guarda' }}
        </button>

        <ProgramsOffCanvas :channel="channel" button-class="btn btn-outline-light" v-if="program">
          <i class="mdi mdi-format-list-bulleted"></i>
          Programmazione
        </ProgramsOffCanvas>
      </div>

      <div class="next-program" v-if="next">
        <span class="label">A seguire</span>
        <strong>{{ formatTime(next.start_tz) }}</strong>
        <span class="title">{{ next.title }}</span>
        <span class="category d-none d-md-inline" v-if="next.category">{{ next.category }}</span>
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
import { formatTime, toUtcDate, useNow } from '../composables/dates'

export default defineComponent({
  name: 'ChannelPreview',
  components: { ProgramsOffCanvas, ChannelProgress },
  setup () {
    const channelsStore = useChannelsStore()
    const now = useNow()

    const channel: ComputedRef<Channel> = computed(() => channelsStore.highlightedChannel)
    const program = computed(() => channel.value?.now_on_air)
    const next = computed(() => channel.value?.next_on_air)
    const bgImage = computed(() => program.value?.cover_img)
    const isPlaying = computed(() => channelsStore.channelPlaying?.id === channel.value?.id)

    const remainingMinutes = computed(() => {
      if (!program.value?.end_tz) {
        return null
      }

      return Math.round((toUtcDate(program.value.end_tz).getTime() - now.value) / 60000)
    })

    function watch () {
      channelsStore.setChannelPlaying(channel.value)
    }

    return {
      channel,
      program,
      next,
      bgImage,
      isPlaying,
      remainingMinutes,
      formatTime,
      watch
    }
  }
})
</script>

<style scoped lang="scss">
@import '../../scss/partials/bsMixins';

.channel-preview {
  height: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: end;
  padding: 3rem;
  overflow: hidden;
  color: white;
}

.cover {
  position: absolute;
  inset: 0;
  background-position: center top;
  background-size: cover;
  transition: background-image .3s;

  &.no-cover {
    background: radial-gradient(circle at 70% 30%, rgba(255, 255, 255, .08) 0%, transparent 60%);
  }

  // Darken from the bottom-left, where the text sits, keeping the image visible top-right
  &:after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(0deg, rgba(var(--bs-dark-rgb), 1) 0%, rgba(var(--bs-dark-rgb), .85) 35%, rgba(var(--bs-dark-rgb), 0) 75%),
    linear-gradient(90deg, rgba(var(--bs-dark-rgb), .8) 0%, rgba(var(--bs-dark-rgb), 0) 60%);
  }
}

.content-container {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: .75rem;
  max-width: 760px;
}

.channel-info {
  display: flex;
  align-items: center;
  gap: .6rem;
  font-weight: 600;

  img {
    height: 32px;
    width: 32px;
    object-fit: contain;
  }

  .dtt-num {
    font-size: .75rem;
    padding: .1rem .4rem;
    border-radius: 4px;
    background: rgba(255, 255, 255, .15);
  }
}

.program-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: .5rem 1rem;
  font-size: .85rem;
  color: rgba(255, 255, 255, .75);

  .badge-live {
    background: var(--bs-danger);
    color: white;
    font-weight: 700;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: .15rem .5rem;
    border-radius: 4px;
  }
}

.program-title {
  margin: 0;
  font-size: clamp(1.4rem, 3vw, 2.6rem);
  font-weight: 700;
  line-height: 1.15;
}

.program-description {
  margin: 0;
  color: rgba(255, 255, 255, .8);
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.remaining {
  margin-top: -.5rem;
  font-size: .8rem;
  color: rgba(255, 255, 255, .6);
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem;
  margin-top: .25rem;

  :deep(.btn) {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-weight: 600;
    padding: .55rem 1.25rem;
  }

  .btn-watch {
    min-width: 140px;

    .mdi {
      font-size: 1.3rem;
      line-height: 1;
    }
  }
}

.next-program {
  display: flex;
  align-items: baseline;
  gap: .6rem;
  margin-top: .5rem;
  padding-top: .75rem;
  border-top: 1px solid rgba(255, 255, 255, .12);
  font-size: .9rem;
  min-width: 0;

  .label {
    flex-shrink: 0;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: rgba(255, 255, 255, .55);
  }

  .title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .category {
    flex-shrink: 0;
    color: rgba(255, 255, 255, .55);
  }
}

@include media-breakpoint-down("md") {
  .channel-preview {
    padding: 1rem 1rem .75rem;
  }

  .content-container {
    gap: .5rem;
  }

  // Keep the mobile header compact: the list needs the space
  .channel-info,
  .program-description,
  .remaining {
    display: none;
  }

  .actions :deep(.btn) {
    padding: .35rem .9rem;
    font-size: .875rem;
  }

  .next-program {
    margin-top: 0;
    padding-top: .5rem;
    font-size: .8rem;
  }
}
</style>

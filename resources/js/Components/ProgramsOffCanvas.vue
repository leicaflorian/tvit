<template>
  <button :class="buttonClass" type="button"
          data-bs-toggle="offcanvas" data-bs-target="#programsOffcanvas"
          aria-controls="programsOffcanvas"
          @click="onShowing">
    <slot>Mostra altri programmi</slot>
  </button>

  <div class="offcanvas offcanvas-end programs-offcanvas" tabindex="-1" id="programsOffcanvas"
       aria-labelledby="programsOffcanvasLabel">

    <div class="offcanvas-header">
      <img :src="channel.logo_url_color" :alt="channel.name" class="channel-logo">

      <div class="header-text">
        <h5 class="offcanvas-title" id="programsOffcanvasLabel">{{ channel.name }}</h5>
        <small>Programmazione di oggi</small>
      </div>

      <button type="button" class="btn btn-light btn-sm btn-watch" data-bs-dismiss="offcanvas" @click="watch">
        <i class="mdi mdi-play"></i> Guarda
      </button>

      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Chiudi"></button>
    </div>

    <div class="offcanvas-body" ref="offcanvasBody">
      <div class="loading" v-if="loading">
        <div class="spinner-border spinner-border-sm" role="status"></div>
        Caricamento programmi…
      </div>

      <p class="empty" v-else-if="!programs.length">Nessun programma disponibile per oggi.</p>

      <section v-for="section in sections" :key="section.label" v-else>
        <h6 class="section-title">{{ section.label }}</h6>

        <ul>
          <SingleProgramCard v-for="program in section.programs" :key="program.id" :program="program"/>
        </ul>
      </section>
    </div>
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, nextTick, PropType, Ref, ref } from 'vue'
import { Channel } from '../@types/Channel'
import { Program } from '../@types/Program'
import SingleProgramCard from './SingleProgramCard.vue'
import { toUtcDate } from '../composables/dates'
import { useChannelsStore } from '../store/channels.js'
import axios from 'axios'

// day parts used to group the schedule, by local start hour
const DAY_PARTS = [
  { label: 'Notte', until: 6 },
  { label: 'Mattina', until: 12 },
  { label: 'Pomeriggio', until: 18 },
  { label: 'Sera', until: 24 }
]

export default defineComponent({
  name: 'ProgramsOffCanvas',
  components: { SingleProgramCard },
  props: {
    channel: Object as PropType<Channel>,
    buttonClass: {
      type: String,
      default: 'btn btn-link'
    }
  },
  setup (props) {
    const channelsStore = useChannelsStore()
    const offcanvasBody: Ref<HTMLElement> = ref()
    const programs: Ref<Program[]> = ref([])
    const loading = ref(false)

    // Chronological sections: a new one starts whenever the day part (or the day) changes
    const sections = computed(() => {
      const today = new Date().toDateString()
      const result = []

      for (const program of programs.value) {
        const start = toUtcDate(program.start_tz)
        const part = DAY_PARTS.find(dayPart => start.getHours() < dayPart.until).label
        const label = start.toDateString() === today ? part : `Domani · ${part}`

        if (result[result.length - 1]?.label !== label) {
          result.push({ label, programs: [] })
        }

        result[result.length - 1].programs.push(program)
      }

      return result
    })

    function scrollToOnAir () {
      const onAirEl: HTMLElement = offcanvasBody.value?.querySelector('.on-air')

      if (!onAirEl) {
        return
      }

      // leave room for the sticky section title and show a bit of what came before
      offcanvasBody.value.scrollTo({ top: Math.max(0, onAirEl.offsetTop - 120) })
    }

    async function onShowing () {
      loading.value = true
      programs.value = []

      try {
        const resp = await axios.get('/api/channels/' + props.channel.tvg_slug)

        programs.value = [...resp.data.programs]
          .sort((a, b) => toUtcDate(a.start_tz).getTime() - toUtcDate(b.start_tz).getTime())
      } finally {
        loading.value = false
      }

      nextTick(scrollToOnAir)
    }

    function watch () {
      channelsStore.setChannelPlaying(props.channel)
    }

    return {
      programs,
      sections,
      loading,
      offcanvasBody,
      onShowing,
      watch
    }
  }
})
</script>

<style scoped lang="scss">
@import '../../scss/partials/bsMixins';

.btn-link {
  --bs-btn-color: var(--bs-light);
  --bs-btn-hover-color: white;
  --bs-btn-active-color: white;
  --bs-btn-focus-box-shadow: none;
}

.programs-offcanvas {
  --bs-offcanvas-width: min(520px, 100vw);
  --bs-offcanvas-bg: rgba(24, 23, 34, .94);
  --bs-offcanvas-border-color: rgba(255, 255, 255, .1);
  --bs-offcanvas-color: white;

  -webkit-backdrop-filter: blur(12px);
  backdrop-filter: blur(12px);
}

.offcanvas-header {
  gap: .75rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, .1);

  .channel-logo {
    width: 40px;
    height: 40px;
    padding: 4px;
    object-fit: contain;
    border-radius: 8px;
    background: #21202E;
  }

  .header-text {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;

    .offcanvas-title {
      font-weight: 700;
    }

    .offcanvas-title,
    small {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    small {
      color: rgba(255, 255, 255, .6);
    }
  }

  .btn-watch {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-weight: 600;
  }

  .btn-close {
    margin: 0;
  }
}

.offcanvas-body {
  padding: 0;
}

.section-title {
  position: sticky;
  top: 0;
  z-index: 1;
  margin: 0;
  padding: .5rem 1.25rem;
  font-size: .72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: rgba(255, 255, 255, .6);
  background: rgba(30, 29, 42, .97);
  border-bottom: 1px solid rgba(255, 255, 255, .07);
}

ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

.loading,
.empty {
  display: flex;
  align-items: center;
  gap: .5rem;
  padding: 1.5rem 1.25rem;
  color: rgba(255, 255, 255, .6);
}
</style>

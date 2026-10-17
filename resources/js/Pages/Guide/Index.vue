<template>
  <Head title="Guida TV"/>

  <div class="guide-page">
    <div class="guide-toolbar">
      <div class="day-chips">
        <button type="button" class="chip" v-for="d in days" :key="d"
                :class="{active: d === day}"
                @click="goToDay(d)">{{ dayLabel(d) }}
        </button>
      </div>

      <button type="button" class="btn btn-sm btn-outline-light" v-if="isToday" @click="scrollToNow(true)">
        <i class="mdi mdi-clock-outline"></i> Adesso
      </button>
    </div>

    <div class="guide-grid" ref="gridEl" :style="gridStyle">
      <div class="grid-inner">
        <!-- time ruler -->
        <div class="ruler">
          <div class="corner"></div>
          <div class="ruler-track">
            <span class="tick" v-for="tick in ticks" :key="tick.left" :style="{left: tick.left + 'px'}">
              {{ tick.label }}
            </span>
          </div>
        </div>

        <div class="guide-row" v-for="channel in channels" :key="channel.id">
          <div class="channel-cell" :title="channel.name">
            <img :src="channel.logo_url_color" :alt="channel.name" loading="lazy">
            <span class="dtt-num" v-if="channel.dtt_num">{{ channel.dtt_num }}</span>
          </div>

          <div class="programs-track">
            <button type="button" class="program"
                    v-for="program in programs[channel.tvg_slug] ?? []" :key="program.id"
                    :style="programStyle(program)"
                    :class="programClass(program)"
                    @click="selectProgram(program, channel)">
              <span class="program-title">{{ program.title }}</span>
              <span class="program-time">{{ formatTime(program.start_tz) }}</span>
            </button>

            <div class="no-programs" v-if="!programs[channel.tvg_slug]?.length">Guida non disponibile</div>
          </div>
        </div>

        <div class="now-line" v-if="nowLeft !== null" :style="{left: `calc(var(--channel-col) + ${nowLeft}px)`}"></div>
      </div>
    </div>

    <Transition name="fade">
      <div class="program-detail" v-if="selected">
        <button type="button" class="btn-close btn-close-white" aria-label="Chiudi" @click="selected = null"></button>

        <div class="detail-channel">
          <img :src="selected.channel.logo_url_color" :alt="selected.channel.name">
          {{ selected.channel.name }}
        </div>

        <h4>{{ selected.program.title }}</h4>

        <div class="detail-meta">
          {{ formatTime(selected.program.start_tz) }} - {{ formatTime(selected.program.end_tz) }}
          <span v-if="selected.program.category">· {{ selected.program.category }}</span>
        </div>

        <p v-if="selected.program.description">{{ selected.program.description }}</p>

        <button type="button" class="btn btn-light btn-sm" v-if="isOnAir(selected.program)"
                @click="watch(selected.channel)">
          <i class="mdi mdi-play"></i> Guarda ora
        </button>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { computed, nextTick, onMounted, PropType, ref, watch as watchProp } from 'vue'
import { Channel } from '../../@types/Channel'
import { Program } from '../../@types/Program'
import { formatTime, toUtcDate, useNow } from '../../composables/dates'
import { useChannelsStore } from '../../store/channels'

const props = defineProps({
  channels: Array as PropType<Channel[]>,
  programs: Object as PropType<Record<string, Program[]>>,
  day: String,
  days: Array as PropType<string[]>,
  dayStart: String,
  dayEnd: String
})

const channelsStore = useChannelsStore()
const now = useNow()
const gridEl = ref<HTMLElement>()
const selected = ref<{ program: Program, channel: Channel } | null>(null)

const isMobile = window.matchMedia('(max-width: 767.98px)').matches
// horizontal scale of the grid
const pxPerMinute = isMobile ? 3 : 4

const dayStartMs = computed(() => toUtcDate(props.dayStart).getTime())
const dayEndMs = computed(() => toUtcDate(props.dayEnd).getTime())
const dayMinutes = computed(() => (dayEndMs.value - dayStartMs.value) / 60000)

const gridStyle = computed(() => ({
  '--track-width': dayMinutes.value * pxPerMinute + 'px'
}))

const isToday = computed(() => now.value >= dayStartMs.value && now.value < dayEndMs.value)

const nowLeft = computed(() => isToday.value ? (now.value - dayStartMs.value) / 60000 * pxPerMinute : null)

const ticks = computed(() => {
  const result = []

  for (let minute = 0; minute < dayMinutes.value; minute += 30) {
    result.push({
      left: minute * pxPerMinute,
      label: formatTime(new Date(dayStartMs.value + minute * 60000).toISOString())
    })
  }

  return result
})

function minutesFromDayStart (date: string) {
  return (toUtcDate(date).getTime() - dayStartMs.value) / 60000
}

function programStyle (program: Program) {
  // programs crossing midnight are clipped to the visible day
  const start = Math.max(0, minutesFromDayStart(program.start_tz))
  const end = Math.min(dayMinutes.value, minutesFromDayStart(program.end_tz))

  return {
    left: start * pxPerMinute + 'px',
    width: Math.max(0, (end - start) * pxPerMinute - 2) + 'px'
  }
}

function isOnAir (program: Program) {
  return toUtcDate(program.start_tz).getTime() <= now.value && toUtcDate(program.end_tz).getTime() > now.value
}

function programClass (program: Program) {
  return {
    'on-air': isOnAir(program),
    past: toUtcDate(program.end_tz).getTime() <= now.value,
    selected: selected.value?.program.id === program.id
  }
}

function selectProgram (program: Program, channel: Channel) {
  selected.value = { program, channel }
}

function watch (channel: Channel) {
  channelsStore.setChannelPlaying(channel)
}

const weekdayFormat = new Intl.DateTimeFormat('it', { weekday: 'short', day: 'numeric' })

function dayLabel (day: string) {
  const index = props.days.indexOf(day)

  if (index === 0) {
    return 'Oggi'
  }

  if (index === 1) {
    return 'Domani'
  }

  // noon avoids any timezone shift on the date
  return weekdayFormat.format(new Date(day + 'T12:00:00'))
}

function goToDay (day: string) {
  selected.value = null
  Inertia.get('/guida', { day }, { preserveState: false })
}

// Other days open on prime time, or on the first program when the guide doesn't reach it yet
function otherDayStartMinutes () {
  const primeTime = 20 * 60
  const all = Object.values(props.programs).flat()

  if (!all.length) {
    return 0
  }

  const channelsAtPrimeTime = Object.values(props.programs).filter(programs => programs.some(program =>
    minutesFromDayStart(program.start_tz) <= primeTime && minutesFromDayStart(program.end_tz) > primeTime
  )).length

  if (channelsAtPrimeTime >= props.channels.length / 3) {
    return primeTime
  }

  return Math.max(0, Math.min(...all.map(program => minutesFromDayStart(program.start_tz))))
}

function scrollToNow (smooth = false) {
  const el = gridEl.value

  if (!el) {
    return
  }

  const target = nowLeft.value ?? otherDayStartMinutes() * pxPerMinute

  el.scrollTo({
    left: Math.max(0, target - el.clientWidth / 4),
    behavior: smooth ? 'smooth' : 'auto'
  })
}

onMounted(() => {
  // landing directly on the guide: the store needs the channels for zapping in the player
  if (!channelsStore.list.length) {
    channelsStore.$patch({ list: props.channels, highlightedChannel: props.channels[0] })
  }

  nextTick(() => scrollToNow())
})

watchProp(() => props.day, () => nextTick(() => scrollToNow()))
</script>

<style scoped lang="scss">
@import '../../../scss/partials/bsMixins';

.guide-page {
  height: 100%;
  display: flex;
  flex-direction: column;
  position: relative;
  color: white;
}

.guide-toolbar {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .75rem 1rem;

  .btn {
    flex-shrink: 0;
    display: inline-flex;
    gap: .3rem;
  }
}

.day-chips {
  display: flex;
  gap: .4rem;
  overflow-x: auto;
  flex: 1;
  min-width: 0;
  scrollbar-width: none;

  &::-webkit-scrollbar {
    display: none;
  }
}

.chip {
  flex-shrink: 0;
  border: 1px solid rgba(255, 255, 255, .2);
  background: transparent;
  color: rgba(255, 255, 255, .8);
  border-radius: 999px;
  padding: .15rem .8rem;
  font-size: .85rem;
  text-transform: capitalize;

  &:hover {
    background: rgba(255, 255, 255, .1);
  }

  &.active {
    background: white;
    border-color: white;
    color: #000;
  }
}

.guide-grid {
  --channel-col: 80px;
  --row-height: 64px;

  flex: 1;
  min-height: 0;
  overflow: auto;
  position: relative;
}

.grid-inner {
  position: relative;
  width: calc(var(--channel-col) + var(--track-width));
}

.ruler,
.guide-row {
  display: flex;
  width: calc(var(--channel-col) + var(--track-width));
}

.ruler {
  position: sticky;
  top: 0;
  z-index: 3;
  height: 32px;
  background: var(--bs-dark);
  border-bottom: 1px solid rgba(255, 255, 255, .1);

  .corner {
    position: sticky;
    left: 0;
    z-index: 1;
    width: var(--channel-col);
    flex-shrink: 0;
    background: var(--bs-dark);
  }
}

.ruler-track {
  position: relative;
  flex: 1;

  .tick {
    position: absolute;
    top: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    padding-left: .4rem;
    border-left: 1px solid rgba(255, 255, 255, .15);
    font-size: .75rem;
    color: rgba(255, 255, 255, .65);
    font-variant-numeric: tabular-nums;
  }
}

.guide-row {
  height: var(--row-height);
  border-bottom: 1px solid rgba(255, 255, 255, .06);
}

.channel-cell {
  position: sticky;
  left: 0;
  z-index: 2;
  width: var(--channel-col);
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #21202E;
  border-right: 1px solid rgba(255, 255, 255, .1);

  img {
    max-width: 60%;
    max-height: 60%;
    object-fit: contain;
  }

  .dtt-num {
    position: absolute;
    bottom: 4px;
    right: 4px;
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

.programs-track {
  position: relative;
  flex: 1;
}

.program {
  position: absolute;
  top: 4px;
  bottom: 4px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  gap: .1rem;
  padding: 0 .5rem;
  overflow: hidden;
  text-align: left;
  border: 1px solid transparent;
  border-radius: 6px;
  background: rgba(255, 255, 255, .07);
  color: white;
  transition: background .15s;

  &:hover {
    background: rgba(255, 255, 255, .14);
  }

  &.past {
    opacity: .45;
  }

  &.on-air {
    background: rgba(255, 255, 255, .18);
    border-color: rgba(255, 255, 255, .35);
  }

  &.selected {
    border-color: white;
  }

  .program-title,
  .program-time {
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .program-title {
    font-size: .8rem;
    font-weight: 600;
  }

  .program-time {
    font-size: .7rem;
    color: rgba(255, 255, 255, .6);
  }
}

.no-programs {
  position: sticky;
  left: var(--channel-col);
  display: inline-flex;
  align-items: center;
  height: 100%;
  padding-left: 1rem;
  font-size: .8rem;
  color: rgba(255, 255, 255, .4);
}

.now-line {
  position: absolute;
  top: 0;
  bottom: 0;
  width: 2px;
  z-index: 1;
  background: var(--bs-danger);
  pointer-events: none;

  &:before {
    content: '';
    position: absolute;
    top: 26px;
    left: -4px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--bs-danger);
  }
}

.program-detail {
  position: absolute;
  right: 1rem;
  bottom: 1rem;
  z-index: 10;
  width: min(380px, calc(100% - 2rem));
  max-height: 60%;
  overflow: auto;
  padding: 1rem 1.25rem;
  border-radius: var(--bs-border-radius-lg);
  border: 1px solid rgba(255, 255, 255, .2);
  background: rgba(33, 32, 46, .92);
  backdrop-filter: blur(10px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, .5);

  .btn-close {
    position: absolute;
    top: .75rem;
    right: .75rem;
  }

  .detail-channel {
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .85rem;
    color: rgba(255, 255, 255, .7);
    margin-bottom: .5rem;

    img {
      height: 24px;
      width: 24px;
      object-fit: contain;
    }
  }

  h4 {
    margin: 0 1.5rem .25rem 0;
    font-size: 1.15rem;
  }

  .detail-meta {
    font-size: .85rem;
    color: rgba(255, 255, 255, .7);
    margin-bottom: .5rem;
  }

  p {
    font-size: .85rem;
    color: rgba(255, 255, 255, .85);
  }

  .btn {
    display: inline-flex;
    gap: .3rem;
  }
}

@include media-breakpoint-down("md") {
  .guide-grid {
    --channel-col: 56px;
    --row-height: 56px;
  }

  .program-detail {
    left: 1rem;
    right: 1rem;
    width: auto;
  }
}
</style>

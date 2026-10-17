<template>
  <li class="program-item" :class="{'on-air': onAir, 'past': past}">
    <div class="time-col">
      <strong>{{ formatTime(program.start_tz) }}</strong>
      <small>{{ program.duration }} min</small>
    </div>

    <div class="content-col">
      <div class="meta" v-if="onAir || program.category">
        <span class="badge-live" v-if="onAir">In onda</span>
        <span v-if="program.category">{{ program.category }}</span>
      </div>

      <h6 class="title">{{ program.title }}</h6>

      <p class="description" v-if="program.description">{{ program.description }}</p>

      <div class="progress-line" v-if="onAir">
        <div :style="{ width: progress + '%' }"></div>
      </div>
    </div>

    <img class="thumb" v-if="program.cover_img" :src="program.cover_img" alt="" loading="lazy">
  </li>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { Program } from '../@types/Program'
import { formatTime, programProgress, toUtcDate, useNow } from '../composables/dates'

const props = defineProps({
  program: Object as PropType<Program>
})

const now = useNow()

const onAir = computed(() => toUtcDate(props.program.start_tz).getTime() <= now.value && toUtcDate(props.program.end_tz).getTime() > now.value)
const past = computed(() => toUtcDate(props.program.end_tz).getTime() <= now.value)
const progress = computed(() => programProgress(props.program, now.value))
</script>

<style scoped lang="scss">
.program-item {
  position: relative;
  display: flex;
  gap: 1rem;
  padding: .9rem 1.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, .07);
  color: white;

  &.past {
    opacity: .5;
  }

  &.on-air {
    background: rgba(255, 255, 255, .07);

    // accent bar on the left edge
    &:before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 3px;
      background: var(--bs-danger);
    }

    .description {
      -webkit-line-clamp: 4;
    }
  }
}

.time-col {
  flex: 0 0 52px;
  display: flex;
  flex-direction: column;
  font-variant-numeric: tabular-nums;

  strong {
    font-size: .95rem;
  }

  small {
    font-size: .72rem;
    color: rgba(255, 255, 255, .5);
  }
}

.content-col {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: .3rem;
}

.meta {
  display: flex;
  align-items: center;
  gap: .6rem;
  font-size: .75rem;
  color: rgba(255, 255, 255, .6);

  .badge-live {
    background: var(--bs-danger);
    color: white;
    font-weight: 700;
    font-size: .62rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    padding: .1rem .4rem;
    border-radius: 4px;
  }
}

.title {
  margin: 0;
  font-size: .95rem;
  font-weight: 600;
  line-height: 1.3;
}

.description {
  margin: 0;
  font-size: .8rem;
  line-height: 1.45;
  color: rgba(255, 255, 255, .7);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.progress-line {
  height: 3px;
  margin-top: .2rem;
  border-radius: 2px;
  background: rgba(255, 255, 255, .15);
  overflow: hidden;

  div {
    height: 100%;
    background: white;
  }
}

.thumb {
  flex: 0 0 auto;
  align-self: flex-start;
  width: 96px;
  aspect-ratio: 16 / 9;
  object-fit: cover;
  border-radius: 6px;
  background: rgba(255, 255, 255, .05);
}

@media (max-width: 575.98px) {
  .program-item {
    padding: .8rem 1rem;
    gap: .75rem;
  }

  .thumb {
    width: 72px;
  }
}
</style>

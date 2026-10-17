<template>
  <div class="d-flex align-items-center" v-if="hasProgram">
    {{ formatTime(channel.now_on_air.start_tz) }}

    <div class="progress flex-fill mx-3">
      <div class="progress-bar" role="progressbar"
           aria-label="Avanzamento programma"
           :style="`width: ${percent}%`"
           :aria-valuenow="percent" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    {{ formatTime(channel.now_on_air.end_tz) }}
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, PropType } from 'vue'
import { Channel } from '../@types/Channel'
import { formatTime } from '../composables/dates'

export default defineComponent({
  name: 'ChannelProgress',
  props: {
    channel: Object as PropType<Channel>
  },
  setup (props) {
    const hasProgram = computed(() => !!(props.channel?.now_on_air?.start_tz && props.channel?.now_on_air?.end_tz))

    function toDate (date: string) {
      return new Date(date.endsWith('Z') ? date : date + 'Z')
    }

    const percent = computed(() => {
      if (!hasProgram.value) {
        return 0
      }

      const start = toDate(props.channel.now_on_air.start_tz).getTime()
      const end = toDate(props.channel.now_on_air.end_tz).getTime()
      const current = (Date.now() - start) / (end - start) * 100

      return Math.min(100, Math.max(0, current))
    })

    return {
      hasProgram,
      formatTime,
      percent
    }
  }
})
</script>

<style scoped lang="scss">
.progress {
  --bs-progress-bar-bg: white;
  --bs-progress-bg: rgba(var(--bs-light-rgb), .5);
  height: 4px;

}

</style>

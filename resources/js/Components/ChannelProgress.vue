<template>
  <div class="d-flex align-items-center">
    {{ formatTime(startAt) }}

    <div class="progress flex-fill mx-3">
      <div class="progress-bar" role="progressbar"
           aria-label="Success example"
           :style="`width: ${percent}%`"
           :aria-valuenow="percent" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    {{ formatTime(endAt) }}
  </div>
</template>

<script lang="ts">
import { computed, defineComponent, PropType } from 'vue'
import { Channel } from '../@types/Channel'

export default defineComponent({
  name: 'ChannelProgress',
  props: {
    channel: Object as PropType<Channel>
  },
  setup (props) {
    const startAt = computed(() => new Date(props.channel?.now_on_air.start_tz))
    const endAt = computed(() => new Date(props.channel?.now_on_air.end_tz))
    const percent = computed(() => {
      const now = new Date()
      const total = endAt.value.getTime() - startAt.value.getTime()
      const current = now.getTime() - startAt.value.getTime()
      return (current / total) * 100
    })

    function formatTime (date: Date) {
      return date.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: false
      })
    }

    return {
      startAt,
      endAt,
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

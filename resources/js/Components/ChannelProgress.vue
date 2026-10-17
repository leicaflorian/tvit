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
import { formatTime, programProgress, useNow } from '../composables/dates'

export default defineComponent({
  name: 'ChannelProgress',
  props: {
    channel: Object as PropType<Channel>
  },
  setup (props) {
    const hasProgram = computed(() => !!(props.channel?.now_on_air?.start_tz && props.channel?.now_on_air?.end_tz))

    const now = useNow()
    const percent = computed(() => programProgress(props.channel?.now_on_air, now.value))

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

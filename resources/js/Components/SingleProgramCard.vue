<template>
  <li class="list-group-item px-0 py-0" :class="{'on-air': program.on_air}">
    <div class="row g-0">
      <div class="col-12 col-md-3 border-end text-center">
        <img :src="coverImg" alt="" :style="coverImgStyle" class="img-fluid ">
      </div>

      <div class="col-12 col-md-9">
        <div class="p-3">
          <div>
            <strong>{{ formatTime(program.start) }} - {{ formatTime(program.end) }}</strong> ({{ program.duration }}')
          </div>
          <div><strong>{{ program.title }}</strong></div>
          <div>{{ program.category }}</div>
          <div><small class="d-block">{{ program.description }}</small></div>
        </div>
      </div>
    </div>
  </li>
</template>

<script lang="ts" setup>
import { computed, PropType } from 'vue'
import { Program } from '../@types/Program'
import { formatTime } from '../composables/dates'
import { Channel } from '../@types/Channel'

const props = defineProps({
  program: Object as PropType<Program>,
  channel: Object as PropType<Channel>
})

const coverImg = computed(() => {
  return props.program.cover_img || props.channel.logo_url_light.replace('width=120', '')
})

const coverImgStyle = computed(() => {
  return {
    objectFit: props.program.cover_img ? 'cover' : 'contain'
  }
})

</script>

<style scoped lang="scss">
.list-group-item-dark {
  background-color: lighten(#ccd7e0, 13);
}

.on-air {
  background-color: var(--bs-secondary);

  &:after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    background-image: url("/assets/live.gif");
    width: 100px;
    height: 40px;
    background-size: 90%;
    background-repeat: no-repeat;
    background-position: center;
    background-color: white;
    border-bottom-right-radius: 1rem;
  }
}
</style>

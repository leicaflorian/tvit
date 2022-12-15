<template>
  <li class="list-group-item px-0 py-0" :class="{'on-air': program.on_air, 'no-img': !program.cover_img}"
      :style="liStyle">
    <!--    <div class="row g-0">-->
    <!--      <div class="col-12 col-md-3 border-end text-center position-relative">
            <img :src="coverImg" alt="" :style="coverImgStyle" class="img-fluid cover-img">

            <div class="category">{{ program.category }}</div>
          </div>-->

    <!--      <div class="col-12 col-md-9">-->
    <div class="p-3">
      <div class="text-light">
        {{ formatTime(program.start) }} - {{ formatTime(program.end) }} ({{ program.duration }}')
      </div>
      <div class="mb-2"><strong>{{ program.title }}</strong></div>
      <div><small class="d-block text-light">{{ description }}</small></div>
    </div>
    <!--      </div>-->
    <!--    </div>-->
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
  return props.program.cover_img || '' //props.channel.logo_url_light.replace('width=120', '')
})

const coverImgStyle = computed(() => {
  return {
    objectFit: props.program.cover_img ? 'cover' : 'contain',
    paddingBottom: props.program.cover_img ? '' : '24px'
  }
})

const liStyle = computed(() => {
  return {
    backgroundImage: `url(${coverImg.value})`,
    objectFit: props.program.cover_img ? 'cover' : 'contain'
  }
})

const description = computed(() => {
  if (!props.program.description) {
    return
  }

  const length = props.program.description.length
  const maxLen = 400
  let toReturn = props.program.description

  console.log('length', length)

  if (length > maxLen) {
    toReturn = props.program.description.substring(0, maxLen) + '...'
  }

  return toReturn
})

</script>

<style scoped lang="scss">
.list-group-item {
  --bs-list-group-border-color: #717274;
  background-position: center;
  background-repeat: no-repeat;

  &:not(.no-img):before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgb(0, 0, 0);
    background: radial-gradient(circle, rgba(var(--bs-dark-rgb), .7) 0%, rgba(var(--bs-dark-rgb), 1) 100%);
  }

  & * {
    position: relative;
  }
}

.list-group-item-dark {
  background-color: lighten(#ccd7e0, 13);
}

.on-air {
  background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.2) 100%);
  border-left-width: 8px !important;
  border-left-color: #fff !important;

  &:after {
    content: "";
    position: absolute;
    top: 0;
    right: 0;
    background-image: url("/assets/live.gif");
    width: 100px;
    height: 30px;
    background-size: 90%;
    background-repeat: no-repeat;
    background-position: center;
    background-color: white;
    border-bottom-left-radius: 1rem;
  }
}

.category {
  width: 100%;
  font-weight: bold;
  background-color: rgba(0, 0, 0, 0.4);
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  position: absolute;
  bottom: 0;
}

.cover-img {
  height: 100%;
  width: 100%;
}

.text-light {
  --bs-light-rgb: 180, 180, 180
}

.border-end {
  --bs-border-color: #717274
}
</style>

<template>
  <button class="btn btn-link " type="button"
          data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom"
          aria-controls="offcanvasBottom"
          @click="onShowing">
    Mostra altri programmi
  </button>

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel"
       ref="offCanvas">

    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasBottomLabel"></h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body px-0" ref="offcanvasBody">
      <ul class="list-group list-group-flush">
        <template v-for="(program, i) in programs" :key="program.id">
          <div class="position-sticky text-light p-3" style="top: 57px"
               v-if="false">
            <h3 class="m-0">Mattino</h3>
          </div>

          <SingleProgramCard :program="program" :channel="channel"/>
        </template>
      </ul>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, nextTick, onMounted, PropType, Ref, ref } from 'vue'
import { Channel } from '../@types/Channel'
import { Program } from '../@types/Program'
import SingleProgramCard from './SingleProgramCard.vue'
import axios from 'axios'

export default defineComponent({
  name: 'ProgramsOffCanvas',
  components: { SingleProgramCard },
  props: {
    channel: Object as PropType<Channel>
  },
  setup (props) {
    const offCanvas: Ref<HTMLElement> = ref()
    const offcanvasBody: Ref<HTMLElement> = ref()
    const programs: Ref<Program[]> = ref([])

    function scrollToOnAir () {
      const onAirEl: HTMLElement = offcanvasBody.value.querySelector('.on-air')
      const rect = onAirEl.getBoundingClientRect()

      offcanvasBody.value.scrollTo({
        top: onAirEl.offsetTop - (offcanvasBody.value.offsetHeight / 2) + (rect.height / 3)
        // behavior: 'smooth'
      })
      console.log(rect)
    }

    async function onShowing () {
      const resp = await axios.get('/api/channels/' + props.channel.tvg_slug)

      debugger
      programs.value = resp.data.programs

      nextTick(() => {
        scrollToOnAir()
      })
    }

    return {
      programs,
      offCanvas,
      offcanvasBody,
      onShowing
    }
  }
})
</script>

<style scoped lang="scss">
@import '../../scss/partials/bsMixins';

.fab {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 10;
}

.btn-link {
  --bs-btn-color: var(--bs-light);
  --bs-btn-hover-color: white;
  --bs-btn-active-color: white;
  --bs-btn-focus-box-shadow: none;
}

.offcanvas.offcanvas-end {
  --bs-offcanvas-bg: rgba(255, 255, 255, .2);
  --bs-offcanvas-border-color: rgba(255, 255, 255, .3);
  --bs-offcanvas-width: 40vw;


  backdrop-filter: blur(10px);

  .list-group {
    --bs-list-group-bg: transparent;
    --bs-list-group-color: white;
  }
}


@include media-breakpoint-down("md") {
  .offcanvas.offcanvas-end {
    --bs-offcanvas-bg: rgba(255, 255, 255, .2);
    --bs-offcanvas-border-color: rgba(255, 255, 255, .3);
    --bs-offcanvas-width: 90vw;
  }
}
</style>

<template>
  <Head title="Canali"/>

  <div class="container-fluid">

    <div class="top-channel-preview">

    </div>

    <div class="bottom-channels-list">
      <div class="d-flex gap-3 overflow-auto">

        <SingleChannelCard v-for="channel in channels" :key="channel.id"
                           :channel="channel"></SingleChannelCard>

      </div>
    </div>

    <!--    <h1 class="mb-3">Lista Canali</h1>-->


    <!--    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 gx-3 gy-5">-->
    <!--      <div class="col" v-for="channel in channels" :key="channel.id">-->

    <!--        <SingleChannelCard :channel="channel"></SingleChannelCard>-->
    <!--      </div>-->
    <!--    </div>-->
  </div>

</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import { useChannelsStore } from '../../store/channels'
import { onMounted } from 'vue'
import SingleChannelCard from '../../Components/SingleChannelCard.vue'

export default {
  components: {
    SingleChannelCard,
    Head
  },
  props: {
    channels: Array
  },
  setup (props) {
    const channelsStore = useChannelsStore()

    onMounted(async () => {
      channelsStore.$patch({
        list: props.channels
      })

      channelsStore.setActiveChannel(props.channels[0])
    })

    return {}
  }
}
</script>

<style scoped lang="scss">
.container-fluid {
  min-height: 100%;
  display: flex;
  flex-direction: column;
}

.top-channel-preview {
  flex: 1;
}

.bottom-channels-list {
  flex: 0;
}
</style>

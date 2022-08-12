<template>
  <Head title="Canali"/>

  <div class="container">
    <h1 class="mb-3">Lista Canali</h1>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 gx-3 gy-5">
      <div class="col" v-for="channel in channels" :key="channel.id">
        <SingleChannelCard :channel="channel"></SingleChannelCard>
      </div>
    </div>
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
    })

    return {}
  }
}
</script>

<style scoped lang="scss">


</style>

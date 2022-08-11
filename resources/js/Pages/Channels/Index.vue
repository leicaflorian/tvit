<template>
  <Head title="Canali"/>

  <div class="container">
    <h1 class="mb-3">Lista Canali</h1>

    <div class="card">
      <table class="table">
        <thead>
        <tr>
          <th></th>
          <th>Nome</th>
          <th>Numero DTT</th>
          <th>Gruppo</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="channel in channels" :key="channel.id">
          <td class="text-center"><img class="" style="width: 80px" :src="channel.logo_url_light"></td>
          <td>
            <h5 class="mb-0">{{ channel.name }}</h5>
            <div class="channel-programs">
              <small class="channel-program-now" v-if="channel.now_on_air">
                <strong>{{ formatTime(channel.now_on_air.start_tz) }}</strong> - {{ channel.now_on_air.title }}
              </small>

              <small class="channel-program-next" v-if="channel.next_on_air">
                <strong>{{ formatTime(channel.next_on_air.start_tz) }}</strong> - {{ channel.next_on_air.title }}
              </small>
            </div>

          </td>
          <td>{{ channel.dtt_num }}</td>
          <td>{{ channel.group }}</td>
          <td>
            <div class="d-flex gap-2">
              <button class="btn btn-secondary" @click="setChannelPlaying(channel)">
                <i class="mdi mdi-play me-2"></i>
                Play
              </button>

              <Link class="btn btn-link" :href="`/channels/${channel.tvg_slug}`">
                <i class="mdi mdi-format-list-text me-2"></i>
                Programmi
              </Link>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>

</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import { formatTime } from '../../composables/dates'
import { useChannelsStore } from '../../store/channels'
import { onMounted } from 'vue'

export default {
  components: {
    Head,
    Link
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

    return {
      formatTime,
      setChannelPlaying: channelsStore.setChannelPlaying

    }
  }
}
</script>

<style scoped>

</style>

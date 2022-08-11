<template>
  <Head :title="`Programmazione su ${channel.name}`"/>

  <div class="container">
    <h1 class="mb-3">Programmazione su {{ channel.name }}</h1>

    <div class="card">
      <table class="table">
        <thead>
        <tr>
          <th>Ora inizio</th>
          <th>Ora fine</th>
          <th>Durata</th>
          <th>In Onda</th>
          <th>Titolo</th>
          <th>Categoria</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="program in channel.programs">
          <td>{{ formatTime(program.start) }}</td>
          <td>{{ formatTime(program.end) }}</td>
          <td>{{ program.duration }}'</td>
          <td>
            <img v-if="program.on_air" src="/assets/live.gif" alt="" style="width: 80px">
          </td>
          <td>
            <strong>{{ program.title }}</strong>
            <div class="d-flex align-items-start">
              <img :src="program.cover_img" alt="" style="width: 60px;" class="me-2">
              <small class="d-block">{{ program.description }}</small>
            </div>
          </td>
          <td>{{ program.category }}</td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/inertia-vue3'
import { formatTime } from '../../composables/dates'

export default {
  components: {
    Head,
    Link
  },
  props: {
    channel: Object
  },
  setup () {

    return {
      formatTime
    }
  }
}
</script>

<style scoped>

</style>

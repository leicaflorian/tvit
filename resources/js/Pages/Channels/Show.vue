<template>
  <Head :title="`Programmazione su ${channel.name}`"/>

  <div class="container">
    <h1 class="mb-3">Programmazione su {{ channel.name }}</h1>

    <ul class="list-group">
      <template v-for="(program, i) in channel.programs" :key="program.id">
        <div class="bg-dark position-sticky text-light p-3" style="top: 57px"
             v-if="false">
          <h3 class="m-0">Mattino</h3>
        </div>

        <SingleProgramCard
            :program="program" :channel="channel"
            :class="{'list-group-item-dark': i % 2 === 0}"/>
      </template>
    </ul>
  </div>
</template>

<script lang="ts" setup>
import { PropType } from 'vue'
import { Head } from '@inertiajs/inertia-vue3'
import { Channel } from '../../@types/Channel'
import SingleProgramCard from '../../Components/SingleProgramCard.vue'

const props = defineProps({
  channel: Object as PropType<Channel>
})
</script>

<style scoped>

</style>

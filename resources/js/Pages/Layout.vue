<template>
  <TheNavbar></TheNavbar>

  <main class="my-3">
    <slot/>
  </main>

  <ThePlayer></ThePlayer>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import ThePlayer from '../Components/ThePlayer.vue'
import TheNavbar from '../Components/TheNavbar.vue'

export default {
  components: {
    Link,
    ThePlayer,
    TheNavbar
  },
  props: () => this.$attrs
}
</script>

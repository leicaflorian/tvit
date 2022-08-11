<template>
  <nav class="navbar border-bottom navbar-dark bg-dark">
    <div class="container-fluid">
      <div class="navbar-brand">TvIt</div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'TheHeader'
}
</script>

<style scoped lang="scss">
  .navbar{
    position: sticky;
    top: 0;
    z-index: 99;
  }
</style>

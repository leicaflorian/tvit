<template>
  <nav class="navbar">
    <div class="container-fluid">
      <div class="navbar-brand ms-auto">Tv Italy</div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'TheHeader'
}
</script>

<style scoped lang="scss">
.navbar {
  --bs-navbar-color: white;
  --bs-navbar-brand-color: white;

  position: absolute;
  top: 0;
  z-index: 99;
  left: 0;
  right: 0;
  background: rgb(0, 0, 0);
  background: linear-gradient(0deg, rgba(var(--bs-dark-rgb), 0) 0%, rgba(var(--bs-dark-rgb), 1) 100%);
}
</style>

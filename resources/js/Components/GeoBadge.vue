<template>
  <span class="geo-badge" title="Disponibile solo dall'Italia" aria-label="Disponibile solo dall'Italia">
    <span class="flag" aria-hidden="true"><i></i><i></i><i></i></span>
    Solo IT
  </span>
</template>

<style scoped lang="scss">
.geo-badge {
  display: inline-flex;
  align-items: center;
  gap: .3rem;
  flex-shrink: 0;
  padding: .05rem .4rem;
  border: 1px solid rgba(255, 255, 255, .25);
  border-radius: 999px;
  font-size: .62rem;
  font-weight: 600;
  line-height: 1.4;
  letter-spacing: .02em;
  color: rgba(255, 255, 255, .75);
  white-space: nowrap;
}

// tiny Italian flag, drawn so it doesn't depend on emoji support
.flag {
  display: inline-flex;
  width: 12px;
  height: 8px;
  border-radius: 1px;
  overflow: hidden;

  i {
    flex: 1;

    &:nth-child(1) {
      background: #009246;
    }

    &:nth-child(2) {
      background: #fff;
    }

    &:nth-child(3) {
      background: #ce2b37;
    }
  }
}
</style>

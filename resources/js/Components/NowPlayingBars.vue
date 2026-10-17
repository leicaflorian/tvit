<template>
  <span class="now-playing-bars" role="img" aria-label="In riproduzione">
    <span></span><span></span><span></span>
  </span>
</template>

<style scoped lang="scss">
// Equalizer-style "now playing" indicator
.now-playing-bars {
  display: inline-flex;
  align-items: flex-end;
  gap: 2px;
  width: 16px;
  height: 14px;

  span {
    flex: 1;
    height: 100%;
    border-radius: 1px;
    background: currentColor;
    transform-origin: bottom;
    animation: bounce 1s ease-in-out infinite;

    &:nth-child(2) {
      animation-delay: -.4s;
    }

    &:nth-child(3) {
      animation-delay: -.7s;
    }
  }
}

@keyframes bounce {
  0%, 100% {
    transform: scaleY(.3);
  }

  50% {
    transform: scaleY(1);
  }
}

@media (prefers-reduced-motion: reduce) {
  .now-playing-bars span {
    animation: none;

    &:nth-child(1) {
      transform: scaleY(.5);
    }

    &:nth-child(3) {
      transform: scaleY(.7);
    }
  }
}
</style>

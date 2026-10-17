<template>
  <div class="channel-zapper" role="group" aria-label="Cambia canale">
    <button type="button" title="Canale precedente (PagSu)" aria-label="Canale precedente"
            @click="channelsStore.zapChannel(-1)">
      <i class="mdi mdi-menu-up"></i>
    </button>
    <span class="label">CH</span>
    <button type="button" title="Canale successivo (PagGiù)" aria-label="Canale successivo"
            @click="channelsStore.zapChannel(1)">
      <i class="mdi mdi-menu-down"></i>
    </button>
  </div>
</template>

<script setup>
import { useChannelsStore } from '../store/channels'

const channelsStore = useChannelsStore()
</script>

<style scoped lang="scss">
// Remote-control style CH ▲▼ pill
.channel-zapper {
  display: inline-flex;
  align-items: center;
  align-self: center;
  height: 36px;
  border: 1px solid rgba(255, 255, 255, .35);
  border-radius: 999px;
  overflow: hidden;

  button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 100%;
    padding: 0;
    border: 0;
    background: transparent;
    color: white;
    transition: background .15s;

    &:hover {
      background: rgba(255, 255, 255, .15);
    }

    .mdi {
      font-size: 1.6rem;
      line-height: 1;
    }
  }

  .label {
    padding: 0 .5rem;
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .05em;
    color: rgba(255, 255, 255, .8);
  }
}
</style>

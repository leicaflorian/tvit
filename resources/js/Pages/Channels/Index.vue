<template>
  <Head title="Canali"/>

  <div class="main-container">
    <div class="left-column">
      <div class="channels-filters">
        <div class="search-box">
          <i class="mdi mdi-magnify"></i>
          <input type="search" class="form-control form-control-sm"
                 placeholder="Cerca canale o programma"
                 aria-label="Cerca canale o programma"
                 :value="channelsStore.search"
                 @input="channelsStore.searchBy($event.target.value)"
                 @keydown.esc="channelsStore.searchBy('')">
        </div>

        <div class="group-chips">
          <button type="button" class="chip" :class="{active: !channelsStore.filterGroup}"
                  @click="channelsStore.filterBy('')">Tutti
          </button>
          <button type="button" class="chip" v-for="group in channelsStore.groups" :key="group"
                  :class="{active: channelsStore.filterGroup === group}"
                  @click="channelsStore.filterBy(group)">{{ groupLabel(group) }}
          </button>
        </div>
      </div>

      <div class="left-channels-list">
        <SingleChannelCard v-for="channel in channelsStore.filteredList" :key="channel.id"
                           :channel="channel"
                           :active="channelsStore.highlightedChannel?.id === channel.id"/>

        <p class="text-light opacity-75 text-center my-4" v-if="!channelsStore.filteredList.length">
          Nessun canale trovato
        </p>
      </div>
    </div>

    <div class="right-channels-preview">
      <ChannelPreview v-if="channelsStore.highlightedChannel"/>
    </div>
  </div>

</template>

<script>
import { Head } from '@inertiajs/inertia-vue3'
import { useChannelsStore } from '../../store/channels'
import { onMounted, onBeforeMount } from 'vue'
import SingleChannelCard from '../../Components/SingleChannelCard.vue'
import ChannelPreview from '../../Components/ChannelPreview.vue'

const groupLabels = {
  rai: 'Rai',
  mediaset: 'Mediaset',
  la7: 'La7',
  sky: 'Sky',
  discovery: 'Discovery',
  antena: 'Antena',
  protv: 'ProTV',
  regionali: 'Regionali'
}

function groupLabel (group) {
  return groupLabels[group] ?? group.charAt(0).toUpperCase() + group.slice(1)
}

export default {
  components: {
    ChannelPreview,
    SingleChannelCard,
    Head
  },
  props: {
    channels: Array
  },
  setup (props) {
    const channelsStore = useChannelsStore()

    onBeforeMount(async () => {
      channelsStore.$patch({
        list: props.channels,
        highlightedChannel: props.channels[0]
      })

      channelsStore.setActiveChannel(props.channels[0])
    })

    return {
      channelsStore,
      groupLabel
    }
  }
}
</script>

<style scoped lang="scss">
@import '../../../scss/partials/bsMixins';

.container-fluid {
  min-height: 100%;
  display: flex;
}

.main-container {
  height: 100%;
  display: flex;
}

.left-column {
  flex: 0 0 clamp(340px, 30vw, 440px);
  display: flex;
  flex-direction: column;
  min-height: 0;
  // without this the chips row min-content width stretches the column
  min-width: 0;
  border-right: 1px solid rgba(255, 255, 255, .08);
}

.channels-filters {
  flex-shrink: 0;
  padding: .75rem 1rem .5rem;
  display: flex;
  flex-direction: column;
  gap: .5rem;
}

.search-box {
  position: relative;

  .mdi {
    position: absolute;
    left: .6rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, .5);
  }

  .form-control {
    padding-left: 2rem;
    background: rgba(255, 255, 255, .08);
    border-color: transparent;
    color: white;

    &::placeholder {
      color: rgba(255, 255, 255, .45);
    }

    &:focus {
      border-color: rgba(255, 255, 255, .4);
      box-shadow: none;
    }
  }
}

.group-chips {
  display: flex;
  gap: .4rem;
  overflow-x: auto;
  scrollbar-width: none;

  &::-webkit-scrollbar {
    display: none;
  }
}

.chip {
  flex-shrink: 0;
  border: 1px solid rgba(255, 255, 255, .2);
  background: transparent;
  color: rgba(255, 255, 255, .8);
  border-radius: 999px;
  padding: .15rem .7rem;
  font-size: .8rem;
  transition: background .2s, color .2s;

  &:hover {
    background: rgba(255, 255, 255, .1);
  }

  &.active {
    background: white;
    border-color: white;
    color: #000;
  }
}

.left-channels-list {
  flex: 1 1 0;
  min-height: 0;
  display: flex;
  flex-direction: column;
  overflow: auto;
  padding: .5rem 1rem 1rem;
  gap: .5rem;
}

.right-channels-preview {
  flex: 1;
  min-width: 0;
}

@include media-breakpoint-down("md") {
  .main-container {
    flex-direction: column;
  }

  // On mobile the preview goes on top, compact, and the list scrolls below it
  .right-channels-preview {
    order: -1;
    flex: 0 0 auto;
  }

  .left-column {
    flex: 1 1 0;
    border-right: 0;
  }
}
</style>

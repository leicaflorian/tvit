<template>
  <nav class="navbar navbar-dark navbar-expand">
    <div class="container-fluid">
      <div class="navbar-brand d-flex align-items-center gap-2">
        <img src="/favicon.svg" alt="" class="brand-logo" width="30" height="30">
        <span class="d-none d-sm-inline">TvIt</span>
      </div>

      <div class="view-switch">
        <Link href="/channels" :class="{active: $page.url.startsWith('/channels')}">
          <i class="mdi mdi-view-list"></i> Canali
        </Link>
        <Link href="/guida" :class="{active: $page.url.startsWith('/guida')}">
          <i class="mdi mdi-view-grid-outline"></i> Guida
        </Link>
      </div>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto gap-2 gap-sm-3 flex-row">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
               data-bs-auto-close="outside" aria-expanded="false">
              <i class="mdi mdi-link-variant"></i>
              <span class="d-none d-md-inline">Usa in un player IPTV</span>
              <span class="d-none d-sm-inline d-md-none">Player IPTV</span>
            </a>

            <div class="dropdown-menu dropdown-menu-dark dropdown-menu-end iptv-menu">
              <p class="iptv-hint">Copia questi link nel tuo player (VLC, Kodi, TiviMate…)</p>

              <div class="iptv-link" v-for="link in links" :key="link.path">
                <div class="iptv-link-label">
                  <strong>{{ link.label }}</strong>
                  <input type="text" readonly class="form-control form-control-sm"
                         :ref="el => inputs[link.path] = el"
                         :value="origin + link.path"
                         :aria-label="link.label"
                         @focus="$event.target.select()">
                  <small class="copy-fallback" v-if="failed === link.path">Premi Ctrl+C (⌘C) per copiare</small>
                </div>

                <button type="button" class="btn btn-sm btn-outline-light" @click="copy(link.path)"
                        :title="`Copia link ${link.label}`">
                  <i class="mdi" :class="copied === link.path ? 'mdi-check' : 'mdi-content-copy'"></i>
                  {{ copied === link.path ? 'Copiato' : 'Copia' }}
                </button>
              </div>

              <div class="iptv-json">
                Formato JSON:
                <a href="/iptv/list.json" target="_blank">canali</a> ·
                <a href="/epg/list.json" target="_blank">guida</a>
              </div>
            </div>
          </li>

          <li class="nav-item d-none d-sm-block">
            <a class="nav-link" aria-current="page" href="https://github.com/leicaflorian/tvit" target="_blank">
              <GhIcon/>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3'
import { ref } from 'vue'
import GhIcon from '../../assets/github.svg'

export default {
  name: 'TheHeader',
  components: {
    GhIcon, Link
  },
  setup () {
    const links = [
      { label: 'Playlist M3U', path: '/iptv/list.m3u' },
      { label: 'Guida EPG (XMLTV)', path: '/epg/list.xml' }
    ]
    const copied = ref(null)
    const failed = ref(null)
    const inputs = {}
    const origin = window.location.origin

    async function copy (path) {
      try {
        await navigator.clipboard.writeText(origin + path)
        copied.value = path
        failed.value = null

        setTimeout(() => {
          if (copied.value === path) {
            copied.value = null
          }
        }, 2000)
      } catch (e) {
        // clipboard not allowed: select the URL so it can be copied by hand
        failed.value = path
        inputs[path]?.focus()
        inputs[path]?.select()
      }
    }

    return {
      links,
      copied,
      failed,
      inputs,
      origin,
      copy
    }
  }
}
</script>

<style scoped lang="scss">
@import '../../scss/partials/bsMixins';

.navbar {
  position: relative;
  z-index: 99;
  flex-shrink: 0;
  background: var(--bs-dark);
  border-bottom: 1px solid rgba(255, 255, 255, .08);

  .navbar-brand {
    font-weight: 700;
    letter-spacing: .02em;

    .brand-logo {
      width: 30px;
      height: 30px;
      // separates the logo's dark tile from the navbar background
      box-shadow: 0 0 0 1px rgba(255, 255, 255, .12);
      border-radius: 7px;
    }
  }

  .view-switch {
    display: flex;
    gap: .25rem;
    padding: 3px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .08);

    a {
      display: inline-flex;
      align-items: center;
      gap: .3rem;
      padding: .2rem .8rem;
      border-radius: 999px;
      font-size: .85rem;
      color: rgba(255, 255, 255, .75);
      text-decoration: none;

      &:hover {
        color: white;
      }

      &.active {
        background: white;
        color: #000;
      }
    }
  }

  @include media-breakpoint-down("sm") {
    .dropdown-toggle:after {
      display: none;
    }
  }

  .iptv-menu {
    width: min(360px, calc(100vw - 2rem));
    padding: .75rem;
    font-size: .85rem;

    .iptv-hint {
      margin-bottom: .5rem;
      color: rgba(255, 255, 255, .6);
    }

    .iptv-link {
      display: flex;
      align-items: center;
      gap: .5rem;
      padding: .5rem 0;
      border-top: 1px solid rgba(255, 255, 255, .1);

      .btn {
        flex-shrink: 0;
        display: inline-flex;
        gap: .25rem;
      }
    }

    .iptv-link-label {
      flex: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: .25rem;

      .form-control {
        background: rgba(255, 255, 255, .08);
        border-color: transparent;
        color: rgba(255, 255, 255, .8);
        font-size: .75rem;
      }

      .copy-fallback {
        color: var(--bs-warning);
      }
    }

    .iptv-json {
      padding-top: .5rem;
      border-top: 1px solid rgba(255, 255, 255, .1);
      color: rgba(255, 255, 255, .6);

      a {
        color: white;
      }
    }
  }

  .nav-link {
    display: flex;
    align-items: center;
    gap: .35rem;
  }
}
</style>

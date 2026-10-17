<template>
  <nav class="navbar navbar-dark navbar-expand">
    <div class="container-fluid">
      <div class="navbar-brand d-flex align-items-center gap-2">
        <i class="mdi mdi-television-classic"></i>
        TvIt
      </div>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto gap-2 gap-sm-3 flex-row">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="mdi mdi-playlist-play"></i>
              <span class="d-none d-sm-inline">Lista IPTV</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="/iptv/list.m3u" target="_blank" title="Lista .m3u">
                <JsonIcon class="me-2"/>
                Lista .m3u8
              </a></li>
              <li><a class="dropdown-item" href="/iptv/list.json" target="_blank" title="Lista .json">
                <JsonIcon class="me-2"/>
                Lista .json
              </a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="mdi mdi-calendar-clock"></i>
              <span class="d-none d-sm-inline">Lista EPG</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="/epg/list.xml" target="_blank" title="Lista .m3u">
                <JsonIcon class="me-2"/>
                Lista .xml
              </a></li>
              <li><a class="dropdown-item" href="/epg/list.json" target="_blank" title="Lista .json">
                <JsonIcon class="me-2"/>
                Lista .json
              </a></li>
            </ul>
          </li>

          <li class="nav-item">
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
import GhIcon from '../../assets/github.svg'
import ListIcon from '../../assets/file-lines-solid.svg'
import JsonIcon from '../../assets/file-code-solid.svg'

export default {
  name: 'TheHeader',
  components: {
    GhIcon, ListIcon, JsonIcon
  },
  setup () {
  }
}
</script>

<style scoped lang="scss">
.navbar {
  position: relative;
  z-index: 99;
  flex-shrink: 0;
  background: var(--bs-dark);
  border-bottom: 1px solid rgba(255, 255, 255, .08);

  .navbar-brand {
    font-weight: 700;
    letter-spacing: .02em;

    .mdi {
      font-size: 1.5rem;
    }
  }

  .nav-link {
    display: flex;
    align-items: center;
    gap: .35rem;
  }
}
</style>

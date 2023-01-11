<template>
  <nav class="navbar navbar-dark navbar-expand">
    <div class="container-fluid">
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto me-3 gap-3 flex-row">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Lista IPTV
            </a>
            <ul class="dropdown-menu">
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
              Lista EPG
            </a>
            <ul class="dropdown-menu">
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

      <div class="navbar-brand">Tv Italy</div>

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

  position: absolute;
  top: 0;
  z-index: 99;
  left: 0;
  right: 0;
  background: rgb(0, 0, 0);
  background: linear-gradient(0deg, rgba(var(--bs-dark-rgb), 0) 0%, rgba(var(--bs-dark-rgb), 1) 100%);
}
</style>

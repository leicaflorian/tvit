// import './bootstrap'
import '../scss/app.scss'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { createPinia } from 'pinia'

import Layout from './Pages/Layout.vue'

InertiaProgress.init()

const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })

const pinia = createPinia()

createInertiaApp({
  resolve: name => {
    
    const page = pages[`./Pages/${name}.vue`].default
    
    // load a default layout if none is specified
    page.layout = page.layout || Layout
    
    return page
  },
  setup ({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .mount(el)
  },
  title: title => `${title} | TvIt`
})

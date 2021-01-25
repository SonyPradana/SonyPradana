import Home from '../views/Home.vue'
import About from '../views/About.vue'

export default {
  mode: 'history',

  routes: [
    {
      path: '/admin/home',
      name: 'home',
      component: Home,
    },
    {
      path: '/admin/about',
      name: 'about',
      component: About,
    }
  ]
}

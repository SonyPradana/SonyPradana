import Home from '../views/Home.vue'
import About from '../views/About.vue'
import PageNotFound from '../views/PageNotFound.vue'
import UserRequest from '../views/user/UserRequest.vue'

export default {
  mode: 'history',

  routes: [
    {
      path: '/admin/user/request',
      name: 'user-request',
      component: UserRequest,
    },

    {
      path: '/admin/home',
      name: 'home',
      component: Home,
    },
    {
      path: '/admin/about',
      name: 'about',
      component: About,
    },
    // page not found adn reddirect
    {
      path: '/admin/user/*',
      component: PageNotFound,
      redirect: { name: 'user-request' }       
    },
    {
      path: '/admin/*',
      component: PageNotFound,
      redirect: { name: 'home' }       
    },
  ]
}

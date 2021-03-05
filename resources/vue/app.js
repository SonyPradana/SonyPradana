import App from './app.vue'
import Vue from 'vue'
import VueRouter from 'vue-router'
import store from "./store";
import routes from './router'

Vue.use(VueRouter)

new Vue({
    router: new VueRouter(routes),
    store,
    render: h => h(App)
}).$mount('#app')
  
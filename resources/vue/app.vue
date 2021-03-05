<template>
  <div id="app" class="md:container md:mx-auto sm:max-w-sm h-screen max-w-md px-4 py-2 grid grid-rows-dashbord grid-cols-dashbord gap-4">
    <Navbar class="row-start-1 row-end-1 col-start-2 col-end-2" />

    <Navigation class="row-start-1 row-end-3 col-start-1 col-end-1" />

    <router-view class="bg-gray-50 dark:bg-gray-800 m-10 row-start-2 row-end-2 col-start-2 col-end-2"></router-view>
  </div>
</template>

<script>
import Navbar from './components/Header.vue'
import Navigation from './components/Navigation.vue'

export default {
  components: {
    Navbar,
    Navigation
  }
}
</script>

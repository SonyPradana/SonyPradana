<template>
  <div>
    <div class="font-semibold h-14 text-blue-800 text-xl flex items-center mb-12">
      <a href="/">Simpus lerep</a>
    </div>
    <ul class="gap-4 flex flex-col">
      <li class="bg-gray-100 dark:bg-gray-400 p-1 rounded-lg ring-1 divide-gray-100 flex justify-center">
        <router-link :to="{ name: 'home' }" class="text-lg text-gray-500 dark:text-gray-100 hover:text-blue-400 dark:hover:text-gray-50">Home</router-link>
      </li>
      <li class="bg-gray-100 dark:bg-gray-400 p-1 rounded-lg ring-1 divide-gray-100 flex justify-center">
        <router-link :to="{ name: 'user-request' }" class="text-lg text-gray-500 dark:text-gray-100 hover:text-blue-400 dark:hover:text-gray-50">User Request</router-link>
      </li>
      <li class="bg-gray-100 dark:bg-gray-400 p-1 rounded-lg ring-1 divide-gray-100 flex justify-center">
        <router-link :to="{ name: 'about' }" class="text-lg text-gray-500 dark:text-gray-100 hover:text-blue-400 dark:hover:text-gray-50">About</router-link>
      </li>
    </ul>
  </div>
</template>

<script>
  export default {

  }
</script>

<style>

</style>

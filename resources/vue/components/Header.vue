<template>
  <div class="flex justify-between items-center border-b-2 border-gray-200 dark:border-gray-600">
    <div class="">
      <h2 class="text-blue-700 ">{{ $route.name }}</h2>
    </div>

    <div class="flex items-center gap-4">
      <input type="text" class="bg-gray-50 dark:bg-gray-600 text-gray-600 dark:text-gray-100 focus:outline-none ring-2 active:ring-1 border-gray-200 p-1 text-sm w-56 font-sans rounded-md" placeholder="search">
      <div><a class="text-gray-800 dark:text-gray-200" href="/admin">refresh</a></div>
      <div class="p-1 border-2 border-blue-400 rounded-full">
        <img class="w-6 h-6 rounded-full bg-light-blue-100" src="/public/data/img/display-picture/user/small-angger.jpg">
      </div>
    </div>

  </div>
</template>

<script>
export default {
}
</script>

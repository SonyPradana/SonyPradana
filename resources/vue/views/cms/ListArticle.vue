<template>
  <div>
    <h3 class="text-3xl font-bold font-sans text-gray-800 dark:text-gray-300 mb-6">List Article</h3>
    <div>
      <table class="table-fixed w-lg text-gray-900 dark:text-gray-200 text-xl text-bold">
        <thead>
          <tr>
            <th class="border border-green-600 dark:text-gray-300 p-2">No</th>
            <th class="border border-green-600 dark:text-gray-300 p-2">Title</th>
            <th class="border border-green-600 dark:text-gray-300 p-2">Date</th>
            <th class="border border-green-600 dark:text-gray-300 p-2">Status</th>
            <th class="border border-green-600 dark:text-gray-300 p-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="feed in news" :key="feed.id">
            <td class="border border-green-600 dark:text-gray-300 text-base p-2">1</td>
            <td class="border border-green-600 dark:text-gray-300 text-base p-2">{{ feed.title }}</td>
            <td class="border border-green-600 dark:text-gray-300 text-base p-2">{{ feed.date }}</td>
            <td class="border border-green-600 dark:text-gray-300 text-base p-2">{{ feed.status }}</td>
            <td class="border border-green-600 dark:text-gray-300 text-base p-2">
              <router-link
                class="bg-lime-400 text-gray-50 py-2 px-4 rounded"
                :to="{ name: 'article-edit', params: {id: feed.id} }"
                tag="button"
                >Edit</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      news: []
    }
  },
  methods: {
    getNews() {
      fetch('/api/ver1.1/NewsFeeder/AllNews.json')
      .then(response => response.json())
      .then(json => {
        if (json.status == 'ok') {
          this.news = json.data
        }
      });
    }
  },
  mounted() {
    this.getNews()
  },
}
</script>

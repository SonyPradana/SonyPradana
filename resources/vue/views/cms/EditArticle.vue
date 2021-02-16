<template>
  <div>
    <h1 class="text-3xl font-bold font-sans text-gray-800 dark:text-gray-300 mb-6">Submit Article Baru</h1>

    <form class="flex flex-col gap-4 max-w-screen-md" v-on:submit="submit($event)">
      <label class="flex flex-col gap-2 text-gray-400" >
        Title
        <input type="text" class="text-gray-900 px-2 py-1 rounded-sm focus:ring ring-blue-500 border-gray-700 focus:outline-none hover:outline-none active:outline-none" name="title" readonly v-model="title">
        <p v-show="isError(error.title)" class="text-red-600 flex justify-end">{{ error.title }}</p>
      </label>

      <label class="flex flex-col gap-2 text-gray-400">
        Keyword
        <input type="text" class="text-gray-900 px-2 py-1 rounded-sm focus:ring ring-blue-500 border-gray-700 focus:outline-none hover:outline-none active:outline-none" name="keywords" v-model="keywords">
        <p v-show="isError(error.keywords)" class="text-red-600 flex justify-end">{{ error.keywords }}</p>
      </label>

      <label class="flex flex-col gap-2 text-gray-400">
        Discription
        <input type="text" class="text-gray-900 px-2 py-1 rounded-sm focus:ring ring-blue-500 border-gray-700 focus:outline-none hover:outline-none active:outline-none" name="discription" v-model="discription">
        <p v-show="isError(error.discription)" class="text-red-600 flex justify-end">{{ error.discription }}</p>
      </label>

      <label class="flex flex-col gap-2 text-gray-400" >
        Image Alt
        <input type="text" class="text-gray-900 px-2 py-1 rounded-sm focus:ring ring-blue-500 border-gray-700 focus:outline-none hover:outline-none active:outline-none" name="image_alt" v-model="image_alt">
        <p v-show="isError(error.image_alt)" class="text-red-600 flex justify-end">{{ error.image_alt }}</p>
      </label>

      <label class="flex flex-col gap-2 text-gray-400" >
        Media note
        <input type="text" class="text-gray-900 px-2 py-1 rounded-sm focus:ring ring-blue-500 border-gray-700 focus:outline-none hover:outline-none active:outline-none" name="media_note" v-model="media_note">
        <p v-show="isError(error.media_note)" class="text-red-600 flex justify-end">{{ error.media_note }}</p>
      </label>

      <label class="flex flex-col gap-2 text-gray-400" >
        Contents
        <textarea class="text-gray-900 px-2 py-1 rounded-sm focus:ring ring-blue-500 border-gray-700 focus:outline-none hover:outline-none active:outline-none" name="content" v-model="content"></textarea>
        <p v-show="isError(error.content)" class="text-red-600 flex justify-end">{{ error.content }}</p>
      </label>

      <section class="flex flex-col gap-2 text-gray-400">
        Status
        <div class="flex gap-2">
          <label>
            <input type="radio" v-model="status" value="draft">
            Draft
          </label>
          <label>
            <input type="radio" v-model="status" value="publish">
            Publish
          </label>
        </div>
      </section>

      <div>
        <button class="bg-lime-400 rounded px-4 py-2">Edit</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      id: null,
      title: '',
      keywords: '',
      discription: '',
      media_note: '',
      image_alt: '',
      content: '',
      status: '',
      error: []
    }
  },
  methods: {
    submit(e) {
      let data = new FormData(e.target);
      data.append('status', this.status)
      data.append('id', this.id)

      this.postData(`/api/ver1.1/Articles/Update-Article.json`, data)
      .then(json => {
        if (json.code == 200) {
          console.table(json.error)
          this.error = json.error
        }
      })

      e.preventDefault();
    },

    isError: obj => typeof obj != "undefined",

    async postData(url = '', data = {}) {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
           // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data // body data type must match "Content-Type" header
      });
      return response.json(); // parses JSON response into native JavaScript objects
    },

    getPost() {
      fetch(`/api/ver1.1/Articles/Review-Article.json?id=${this.id}`)
      .then(response => response.json())
      .then(json => {
        if (json.status == 'ok') {
          this.title = json.data.title;
          this.keywords = json.data.keywords;
          this.discription = json.data.discription;
          this.media_note = json.data.media_note;
          this.image_alt = json.data.image_alt;
          this.content = json.data.raw_content;
          this.status = json.data.status;
        }
      });
    }

  },
  mounted() {
    this.id = this.$route.params.id;
    this.getPost();
  },
}
</script>

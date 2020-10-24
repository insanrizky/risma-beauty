<template>
  <div
    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
  >
    <div class="flex-1 flex justify-between sm:hidden">
      <a
        v-if="pagination.prev_page_url"
        @click="goTo(pagination.current_page - 1)"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
      >
        Sebelumnya
      </a>
      <a
        v-if="pagination.next_page_url"
        @click="goTo(pagination.current_page + 1)"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
      >
        Berikutnya
      </a>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm leading-5 text-gray-700">
          Menampilkan
          <span class="font-medium">{{ pagination.from || 0 }}</span>
          hingga
          <span class="font-medium">{{ pagination.to || 0 }}</span>
          dari
          <span class="font-medium">{{ pagination.total }}</span>
          hasil
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex shadow-sm">
          <a
            v-if="pagination.prev_page_url"
            @click="goTo(pagination.current_page - 1)"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-500 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
            aria-label="Previous"
          >
            <!-- Heroicon name: chevron-left -->
            <svg
              class="h-5 w-5"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </a>
          <!-- <a
            v-for="x in Array(pagination.total_page)"
            :key="x"
            @click="goTo(x)"
            class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
          >
            {{ x }}
          </a>
          <span
            v-if="
              pagination.current_page > 3 &&
              pagination.current_page < pagination.last_page - 3
            "
            class="-ml-px relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700"
          >
            ...
          </span> -->
          <a
            v-if="pagination.next_page_url"
            @click="goTo(pagination.current_page + 1)"
            class="-ml-px relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-500 hover:text-gray-400 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150"
            aria-label="Next"
          >
            <!-- Heroicon name: chevron-right -->
            <svg
              class="h-5 w-5"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"
              />
            </svg>
          </a>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["pagination"],
  methods: {
    goTo(page) {
      this.$emit("event", page);
    },
  },
};
</script>
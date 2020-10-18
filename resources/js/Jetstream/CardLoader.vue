<template>
  <div>
    <div
      v-for="x in Array(total)"
      :key="x"
      class="border border-gray-300 shadow rounded-md p-4 max-w-sm w-full mx-auto bg-white mb-4"
    >
      <div class="animate-pulse flex space-x-4">
        <div class="rounded-full bg-gray-400 h-12 w-12"></div>
        <div class="flex-1 space-y-2 py-1">
          <div class="h-4 bg-gray-400 rounded w-4/6"></div>
          <div class="h-4 bg-gray-400 rounded w-5/6"></div>
        </div>
      </div>
      <div class="animate-pulse space-x-4 space-y-2 mt-4">
        <div class="h-4 bg-gray-400 rounded w-6/6"></div>
      </div>
      <div class="animate-pulse space-x-4 space-y-2 mt-2">
        <div class="h-4 bg-gray-400 rounded w-5/6"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    props: ['total']
};
</script>
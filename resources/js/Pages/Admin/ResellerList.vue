<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Reseller
      </h2>
    </template>

    <div class="bg-white pl-4 py-3 border-t flex items-center">
      ID Agen:
      <chip-label :bgColor="'bg-purple-500'" class="ml-2">{{ $page.identifier }}</chip-label>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import ChipLabel from "./../../Jetstream/ChipLabel";

export default {
  components: {
    AppLayout,
    ChipLabel,
  },

  mounted() {
    console.log(this.$page);
  },
};
</script>
<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Pengaturan
      </h2>
    </template>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <point-conversion />
    </div>
    <div class="max-w-7xl mx-auto py-2 sm:px-6 lg:px-8">
      <with-drawal />
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import PointConversion from "./../../Pages/Admin/PointConversion";
import WithDrawal from "./../../Pages/Admin/WithDrawal";

export default {
  components: {
    AppLayout,
    PointConversion,
    WithDrawal,
  },
};
</script>
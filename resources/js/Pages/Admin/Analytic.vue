<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Analitik
      </h2>
    </template>

    <analytic-users />
    <analytic-claims />
  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import AnalyticUsers from "./../../Jetstream/AnalyticUsers";
import AnalyticClaims from "./../../Jetstream/AnalyticClaims";
export default {
  components: {
    AppLayout,
    AnalyticUsers,
    AnalyticClaims,
  },
};
</script>
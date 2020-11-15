<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Analitik
      </h2>
    </template>

    <analytic-users />

    <!-- <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden sm:rounded-lg">
          <div class="grid sm:grid-cols-2 lg:grid-cols-3">
            <div class="lg:col-span-2 bg-white p-4 mx-2">
              <div class="text-xl font-bold mb-3">Klaim (Waktu)</div>
              <line-chart
                :chartData="this.chartData"
                :options="this.chart_options"
              />
            </div>
            <div class="bg-white p-4 mx-2">
              <div class="text-xl font-bold mb-3">Status Klaim</div>
              <pie-chart
                :chartData="this.chartData"
                :options="this.chart_options"
              />
            </div>
          </div>
        </div>
      </div>
    </div> -->
  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import AnalyticUsers from "./../../Jetstream/AnalyticUsers";
export default {
  components: {
    AppLayout,
    AnalyticUsers,
  },
};
</script>
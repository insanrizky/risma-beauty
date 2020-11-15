<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="overflow-hidden sm:rounded-lg">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3">
          <div class="lg:col-span-2 bg-white p-4 mx-2">
            <div class="text-xl font-bold mb-3">
              {{ typeInWord }} berdasarkan Area
            </div>
            <div class="flex items-center mb-3">
              <select
                v-model="type"
                @change="fetchUserAnalytic"
                class="form-input mt-1 rounded"
              >
                <option value="">Semua</option>
                <option value="AGENT">Agen</option>
                <option value="RESELLER">Reseller</option>
              </select>
              <select-province
                class="ml-3"
                @input="onChangeProvince($event)"
                :value="province_id"
              />
              <!-- <select-city
                  class="ml-3"
                  @input="onChangeCity($event)"
                  :province_id="province_id"
                  :value="city_id"
                /> -->
            </div>
            <div class="mb-3">
              <label v-if="!isShowingAll" class="ml-3 font-bold text-gray-500">
                <input
                  @click="showAll"
                  :checked="isShowingAll"
                  class="mr-1 leading-tight"
                  type="checkbox"
                />
                <span class="text-sm"> Tampilkan Semua Area</span>
              </label>
            </div>
            <bar-chart
              :chartData="this.chart_user_area"
              :options="this.chart_options"
            />
          </div>
          <div class="bg-white p-4 mx-2">
            <div class="text-xl font-bold mb-3">Status {{ typeInWord }}</div>
            <pie-chart
              :chartData="this.chartData"
              :options="this.chart_options"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import BarChart from "./../Charts/Bar";
import LineChart from "./../Charts/Line";
import PieChart from "./../Charts/Pie";
import SelectProvince from "./../Jetstream/SelectProvince";
import SelectCity from "./../Jetstream/SelectCity";

export default {
  components: {
    BarChart,
    LineChart,
    PieChart,
    SelectProvince,
    SelectCity,
  },
  data() {
    return {
      type: "",
      isShowingAll: true,
      province_id: "",
      chart_user_area: {
        labels: [],
        datasets: [],
      },
      chartData: {
        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
        datasets: [
          {
            label: "# of Votes",
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
              "rgba(255, 99, 132, 0.2)",
              "rgba(54, 162, 235, 0.2)",
              "rgba(255, 206, 86, 0.2)",
              "rgba(75, 192, 192, 0.2)",
              "rgba(153, 102, 255, 0.2)",
              "rgba(255, 159, 64, 0.2)",
            ],
            borderColor: [
              "rgba(255, 99, 132, 1)",
              "rgba(54, 162, 235, 1)",
              "rgba(255, 206, 86, 1)",
              "rgba(75, 192, 192, 1)",
              "rgba(153, 102, 255, 1)",
              "rgba(255, 159, 64, 1)",
            ],
            borderWidth: 1,
          },
        ],
      },
      chart_options: {
        maintainAspectRatio: false,
      },
    };
  },
  computed: {
    typeInWord() {
      switch (this.type) {
        case "":
          return "Agen & Reseller";
        case "AGENT":
          return "Agen";
        case "RESELLER":
          return "Reseller";
        default:
          return "-";
      }
    },
  },
  mounted() {
    this.fetchUserAnalytic();
  },
  methods: {
    async fetchUserAnalytic() {
      try {
        const {
          data: { data },
        } = await axios.get("/api/analytic/user/area", {
          params: {
            type: this.type,
            province_id: this.province_id || undefined,
          },
        });

        this.mapChartUserArea(data.attributes);
      } catch (e) {
        console.log(e);
      }
    },
    mapChartUserArea(data) {
      this.chart_user_area = {
        labels: data.map(d => d.name),
        datasets: [
          {
            label: this.typeInWord,
            backgroundColor: '#4BC0C0',
            data: data.map(d => d.total),
          }
        ],
      };
    },
    onChangeProvince(value) {
      this.province_id = value;
      this.isShowingAll = false;
      this.fetchUserAnalytic();
    },
    showAll() {
      this.province_id = "";
      this.isShowingAll = !this.isShowingAll;
      this.fetchUserAnalytic();
    },
  },
};
</script>
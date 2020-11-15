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
                @change="updateChart"
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
              :chartData="this.chart_user_status"
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

const COLORS = {
  AKTIF: {
    BACKGROUND: "rgba(75, 192, 192, 0.2)",
    BORDER: "rgba(75, 192, 192, 1)",
  },
  "AKUN DINONAKTIFKAN": {
    BACKGROUND: "rgba(0, 0, 0, 0.2)",
    BORDER: "rgba(0, 0, 0, 0.7)",
  },
  "DALAM PROSES PENDAFTARAN": {
    BACKGROUND: "rgba(255, 206, 86, 0.2)",
    BORDER: "rgba(255, 206, 86, 1)",
  },
  "GAGAL VERIFIKASI": {
    BACKGROUND: "rgba(255, 99, 132, 0.2)",
    BORDER: "rgba(255, 99, 132, 1)",
  },
  "SEDANG DIVERIFIKASI": {
    BACKGROUND: "rgba(153, 102, 255, 0.2)",
    BORDER: "rgba(153, 102, 255, 1)",
  },
};

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
      chart_user_status: {
        labels: [],
        datasets: [
          {
            label: `Status ${this.typeInWord}`,
            data: [],
            backgroundColor: [],
            borderColor: [],
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
    this.updateChart();
  },
  methods: {
    updateChart() {
      this.fetchUserAnalytic();
      this.fetchUserStatusAnalytic();
    },
    async fetchUserAnalytic() {
      try {
        const {
          data: { data },
        } = await axios.get("/api/analytic/area/user", {
          params: {
            type: this.type,
            province_id: this.province_id || undefined,
          },
        });

        this.mapChartAreaUser(data.attributes.report);
      } catch (e) {
        console.log(e);
      }
    },
    async fetchUserStatusAnalytic() {
      try {
        const {
          data: { data },
        } = await axios.get("/api/analytic/area/user/status", {
          params: {
            type: this.type,
            province_id: this.province_id || undefined,
          },
        });

        this.mapChartAreaUserStatus(data.attributes.report);
      } catch (e) {
        console.log(e);
      }
    },
    mapChartAreaUser(data) {
      this.chart_user_area = {
        labels: data.map((d) => d.name),
        datasets: [
          {
            label: `${this.typeInWord} (AKTIF)`,
            backgroundColor: "rgba(75, 192, 192, 0.7)",
            data: data.map((d) => d.total),
          },
        ],
      };
    },
    mapChartAreaUserStatus(data) {
      const chart_data = [];
      const chart_labels = [];
      const chart_background_color = [];
      const chart_border_color = [];
      data.forEach((d) => {
        chart_labels.push(d.status);
        chart_data.push(d.total);
        chart_background_color.push(COLORS[d.status].BACKGROUND);
        chart_border_color.push(COLORS[d.status].BORDER);
      });

      this.chart_user_status = {
        labels: chart_labels,
        datasets: [
          {
            label: this.typeInWord,
            data: chart_data,
            backgroundColor: chart_background_color,
            borderColor: chart_border_color,
            borderWidth: 1,
          },
        ],
      };
    },
    onChangeProvince(value) {
      this.province_id = value;
      this.isShowingAll = false;
      this.updateChart();
    },
    showAll() {
      this.province_id = "";
      this.isShowingAll = !this.isShowingAll;
      this.updateChart();
    },
  },
};
</script>
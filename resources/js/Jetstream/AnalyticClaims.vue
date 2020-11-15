<template>
  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="overflow-hidden sm:rounded-lg">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3">
          <div class="lg:col-span-2 bg-white p-4 mx-2">
            <div class="text-xl font-bold mb-3">Klaim berdasarkan Waktu</div>

            <div class="flex items-center mb-3">
              <select
                v-model="type"
                @change="updateChart"
                class="type-padding-adjusted form-input mr-3 rounded"
              >
                <option value="">Semua</option>
                <option value="AGENT">Agen</option>
                <option value="RESELLER">Reseller</option>
              </select>
              <date-picker
                v-model="date_range"
                @change="changeDate($event)"
                :range="true"
                type="datetime"
                valueType="format"
                :confirm="true"
                class="date-picker"
              />
            </div>
            <div class="flex mb-3">
              <div class="flex items-center">
                <input
                  type="radio"
                  @change="changeDatePreset"
                  :checked="date_preset === 'last-week'"
                  value="last-week"
                  name="date_preset"
                  id="last-week"
                  class="form-radio"
                />
                <label for="last-week" class="font-sm ml-2"
                  >1 minggu terakhir</label
                >
              </div>
              <div class="flex items-center ml-3">
                <input
                  type="radio"
                  @change="changeDatePreset"
                  :checked="date_preset === 'last-month'"
                  value="last-month"
                  name="date_preset"
                  id="last-month"
                  class="form-radio"
                />
                <label for="last-month" class="font-sm ml-2"
                  >1 bulan terakhir</label
                >
              </div>
            </div>
            <line-chart
              :chartData="this.chart_claim_time"
              :options="this.chart_options"
            />
          </div>
          <div class="bg-white p-4 mx-2">
            <div class="text-xl font-bold">Status Klaim {{ typeInWord }}</div>
            <div
              v-if="startDateFormatted && endDateFormatted"
              class="text-sm mb-3"
            >
              {{ startDateFormatted }} - {{ endDateFormatted }}
            </div>
            <pie-chart
              :chartData="this.chart_claim_status"
              :options="this.chart_options"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.type-padding-adjusted {
  padding-top: 5px;
  padding-bottom: 5px !important;
}
</style>

<script>
import "vue2-datepicker/index.css";
import LineChart from "./../Charts/Line";
import PieChart from "./../Charts/Pie";
import DatePicker from "vue2-datepicker";
import { DateTime } from "luxon";
import _ from "lodash";

const STATUS = [
  "KLAIM DITERIMA",
  "MENUNGGU DIVERIFIKASI",
  "GAGAL VERIFIKASI",
  "KLAIM DICAIRKAN",
];

const STATUS_COLOR = {
  "KLAIM DITERIMA": {
    BACKGROUND: "rgba(75, 192, 192, 0.2)",
    BORDER: "rgba(75, 192, 192, 0.7)",
  },
  "MENUNGGU DIVERIFIKASI": {
    BACKGROUND: "rgba(255, 206, 86, 0.2)",
    BORDER: "rgba(255, 206, 86, 1)",
  },
  "GAGAL VERIFIKASI": {
    BACKGROUND: "rgba(255, 99, 132, 0.2)",
    BORDER: "rgba(255, 99, 132, 0.7)",
  },
  "KLAIM DICAIRKAN": {
    BACKGROUND: "rgba(153, 102, 255, 0.2)",
    BORDER: "rgba(153, 102, 255, 0.3)",
  },
};
const DATE_TIME_FORMAT = "y-MM-dd HH:mm:ss";
const DATE_FORMAT = "y-MM-dd";
const DATE_MONTH_FORMAT = "dd MMM";
const DATE_MONTH_YEAR_FORMAT = "dd MMMM y";
const INITIAL_CHART = {
  labels: [],
  datasets: [
    {
      label: "",
      data: [],
      backgroundColor: [],
      borderColor: [],
    },
  ],
};

export default {
  components: {
    LineChart,
    PieChart,
    DatePicker,
  },
  data() {
    return {
      type: "",
      date_preset: "last-month",
      date_range: [
        DateTime.local().minus({ months: 1 }).toFormat(DATE_TIME_FORMAT),
        DateTime.local().toFormat(DATE_TIME_FORMAT),
      ],
      chart_claim_time: INITIAL_CHART,
      chart_claim_status: INITIAL_CHART,
      chart_options: {
        maintainAspectRatio: false,
      },
    };
  },
  computed: {
    startDateFormatted() {
      return this.date_range[0]
        ? DateTime.fromSQL(this.date_range[0]).toFormat(DATE_MONTH_YEAR_FORMAT)
        : null;
    },
    endDateFormatted() {
      return this.date_range[1]
        ? DateTime.fromSQL(this.date_range[1]).toFormat(DATE_MONTH_YEAR_FORMAT)
        : null;
    },
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
    changeDatePreset($event) {
      this.date_preset = $event.target.value;
      switch (this.date_preset) {
        case "last-week":
          this.date_range = [
            DateTime.local().minus({ days: 7 }).toFormat(DATE_TIME_FORMAT),
            DateTime.local().toFormat(DATE_TIME_FORMAT),
          ];
          break;
        case "last-month":
          this.date_range = [
            DateTime.local().minus({ months: 1 }).toFormat(DATE_TIME_FORMAT),
            DateTime.local().toFormat(DATE_TIME_FORMAT),
          ];
          break;
        default:
          break;
      }
      this.updateChart();
    },
    changeDate($event) {
      this.date_range = $event;
      this.date_preset = "";
      this.updateChart();
    },
    updateChart() {
      if (this.date_range[0] && this.date_range[1]) {
        this.fetchClaimTimeAnalytic();
        return;
      }

      this.chart_claim_time = INITIAL_CHART;
      this.chart_claim_status = INITIAL_CHART;
    },
    async fetchClaimTimeAnalytic() {
      try {
        const {
          data: { data },
        } = await axios.get("/api/analytic/claim", {
          params: {
            type: this.type,
            start_date: this.date_range[0],
            end_date: this.date_range[1],
          },
        });
        this.mapChartClaimTime(data.attributes.report);
      } catch (e) {
        console.log(e);
      }
    },
    getDateInRange(start, end) {
      const start_date = DateTime.fromSQL(start);
      const end_date = DateTime.fromSQL(end);
      const diff = end_date.diff(start_date).as("days");

      const date_range = [];
      let i = start_date;
      while (date_range.length < diff) {
        date_range.push(i.toFormat(DATE_FORMAT));
        i = i.plus({ days: 1 });
      }
      return date_range;
    },
    mapChartClaimTime(data) {
      const date_lines = this.getDateInRange(
        this.date_range[0],
        this.date_range[1]
      );

      const chart_claim_status_data = [];
      const chart_claim_status_background_color = [];
      const chart_claim_status_border_color = [];
      const chart_claim_time_datasets = [];
      STATUS.forEach((status) => {
        const dataset = {
          label: status,
          data: date_lines.map((date) => {
            const findStatusInDate = data.find(
              (d) => d.date === date && d.status === status
            );
            if (findStatusInDate) {
              return findStatusInDate.total;
            }
            return 0;
          }),
          backgroundColor: STATUS_COLOR[status].BACKGROUND,
          borderColor: STATUS_COLOR[status].BORDER,
          fill: false,
        };
        chart_claim_time_datasets.push(dataset);

        chart_claim_status_data.push(
          dataset.data.reduce((prev, curr) => prev + curr)
        );
        chart_claim_status_background_color.push(dataset.backgroundColor);
        chart_claim_status_border_color.push(dataset.borderColor);
      });

      this.chart_claim_time = {
        labels: date_lines.map((date) =>
          DateTime.fromSQL(date).toFormat(DATE_MONTH_FORMAT)
        ),
        datasets: chart_claim_time_datasets,
      };
      this.chart_claim_status = {
        labels: STATUS,
        datasets: [
          {
            label: "Status Klaim",
            data: chart_claim_status_data,
            backgroundColor: chart_claim_status_background_color,
            borderColor: chart_claim_status_border_color,
            borderWidth: 1,
          },
        ],
      };
    },
  },
};
</script>
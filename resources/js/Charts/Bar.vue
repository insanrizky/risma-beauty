<script>
import { Bar } from "vue-chartjs";
export default {
  extends: Bar, // or mixins: [Bar],
  props: ["chartData", "options"],
  mounted() {
    this.renderChart(this.chartData, this.options);
  },
};
</script>
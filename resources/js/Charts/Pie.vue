<script>
import { Pie } from "vue-chartjs";
export default {
  extends: Pie, // or mixins: [Pie],
  props: ["chartData", "options"],
  mounted() {
    this.renderChart(this.chartData, this.options);
  },
};
</script>
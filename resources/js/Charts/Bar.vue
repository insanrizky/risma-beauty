<script>
import { Bar, mixins } from "vue-chartjs";

export default {
  mixins: [Bar, mixins.reactiveProp],
  props: ["chartData", "options"],
  mounted() {
    this.renderChart(this.chartData, this.options);
  },
};
</script>
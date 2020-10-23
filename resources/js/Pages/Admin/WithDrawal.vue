<template>
  <jet-form-section>
    <template #title> Pencairan Klaim </template>

    <template #description>
      Pilih rentang waktu untuk mencairkan klaim yang sudah diverifikasi.
    </template>

    <template #form>
      <div class="col-span-6 sm:col-span-4">
        <jet-label value="Rentang Waktu" />
        <date-picker
          v-model="date_range"
          @change="changeDate($event)"
          range
          type="datetime"
          valueType="format"
          confirm
        />
      </div>

      <div v-if="hasCompleteCalculations" class="col-span-6 sm:col-span-4">
        <div class="mb-4">
          <chip-label :bgColor="'bg-purple-500'">Total Poin Agen:</chip-label>
          <div class="mt-2">
            {{ total_point_agent }} poin x
            {{ formatRupiah(agent_multiplier) }} =
            {{ formatRupiah(totalAmountAgent) }}
          </div>
        </div>
        <div class="mb-4">
          <chip-label :bgColor="'bg-blue-500'">Total Poin Reseller:</chip-label>
          <div class="mt-2">
            {{ total_point_reseller }} poin x
            {{ formatRupiah(reseller_multiplier) }} =
            {{ formatRupiah(totalAmountReseller) }}
          </div>
        </div>

        <div class="mb-6">
          <chip-label :bgColor="'bg-green-500'">Total untuk Dicairkan</chip-label>
          <div class="mt-2">
            {{ formatRupiah(totalAmountToWithDraw) }}
          </div>
        </div>
      </div>
    </template>

    <template #actions>
      <a :href="exportUrl" :target="isExportable && '_blank'">
        <div
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150 bg-green-500"
          :class="{ 'opacity-25': is_processing || !isExportable }"
        >
          Export
        </div>
      </a>

      <jet-button
        class="bg-red-600 ml-2"
        :class="{ 'opacity-25': is_processing || !isExportable }"
        :disabled="!isExportable"
        @click.native.prevent="withDrawal"
      >
        Cairkan!
      </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
import "vue2-datepicker/index.css";

import JetFormSection from "./../../Jetstream/FormSection";
import JetButton from "./../../Jetstream/Button";
import JetInput from "./../../Jetstream/Input";
import JetLabel from "./../../Jetstream/Label";
import JetInputError from "./../../Jetstream/InputError";
import JetActionMessage from "./../../Jetstream/ActionMessage";
import JetSecondaryButton from "./../../Jetstream/SecondaryButton";
import ChipLabel from "./../../Jetstream/ChipLabel";
import DatePicker from "vue2-datepicker";
import { formatRupiah } from "../../helpers";

export default {
  components: {
    JetFormSection,
    JetButton,
    JetSecondaryButton,
    JetInput,
    JetLabel,
    JetInputError,
    JetActionMessage,
    DatePicker,
    ChipLabel,
  },

  data() {
    return {
      date_range: [],
      is_processing: false,
      agent_multiplier: 0,
      reseller_multiplier: 0,
      total_point_agent: 0,
      total_point_reseller: 0,
    };
  },

  computed: {
    totalAmountToWithDraw() {
      return this.totalAmountAgent + this.totalAmountReseller;
    },
    totalAmountReseller() {
      return this.total_point_reseller * this.reseller_multiplier;
    },
    totalAmountAgent() {
      return this.total_point_agent * this.agent_multiplier;
    },
    isExportable() {
      return this.exportUrl !== "#";
    },
    exportUrl() {
      if (this.date_range.length < 2) {
        return "#";
      }
      return `/api/export?start_date=${this.date_range[0]}&end_date=${this.date_range[1]}`;
    },
    hasCompleteCalculations() {
      return this.agent_multiplier && this.reseller_multiplier;
    },
  },

  methods: {
    formatRupiah,
    changeDate($event) {
      this.date_range = $event;
      this.calculateInRange();
    },
    async calculateInRange() {
      try {
        const {
          data: {
            meta: {
              total_point_agent,
              total_point_reseller,
              agent_multiplier,
              reseller_multiplier,
            },
          },
        } = await axios.get("/api/point/calculate", {
          params: {
            start_date: this.date_range[0],
            end_date: this.date_range[1],
          },
        });

        this.total_point_agent = total_point_agent;
        this.total_point_reseller = total_point_reseller;
        this.agent_multiplier = agent_multiplier;
        this.reseller_multiplier = reseller_multiplier;
      } catch (e) {
        console.log(e);
        this.$swal("Terjadi Kesalahan!", "", "error");
      }
    },
    async withDrawal() {
      try {
        this.is_processing = true;
        await axios.post('/api/point/withdrawal', {
          start_date: this.date_range[0],
          end_date: this.date_range[1],
        });
        this.is_processing = false;
        this.resetData();
        this.$swal("Berhasil!", "Klaim telah dicairkan", "success");
      } catch (e) {
        console.log(e);
        this.$swal("Terjadi Kesalahan!", "", "error");
        this.is_processing = false;
      }
    },
    resetData() {
      this.date_range = [];
      this.total_point_agent = 0;
      this.total_point_reseller = 0;
      this.agent_multiplier = null;
      this.reseller_multiplier = null;
    }
  },
};
</script>
<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Peringkat
      </h2>
    </template>

    <div v-if="$page.user.type === 'ADMIN'" class="bg-white border-t">
      <div class="flex max-w-7xl mx-auto sm:px-4 px-0">
        <div class="bg-white pl-4 py-3">Filter:</div>
        <select
          v-model="filter"
          @change="changeFilter($event)"
          class="form-input focus-none w-full rounded border-0"
        >
          <option>Semua</option>
          <option>Agent</option>
          <option>Reseller</option>
        </select>
      </div>
    </div>

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden sm:rounded-lg">
          <div class="px-4 py-2 mb-4 bg-green-500 text-white rounded">
            <div class="font-bold text-xl">
              Total ({{ filter }}): {{ total_point }} Poin
            </div>
            <div v-if="getFilter !== 'Semua'" class="font-bold text-l">
              x {{ formatRupiah(multiplier) }} =
              {{ formatRupiah(total_point * multiplier) }}
            </div>
            <div class="text-m">Dari {{ total_member }} member</div>
          </div>

          <div v-if="is_fetching">
            <card-loader :total="3" />
          </div>
          <div v-else>
            <div
              v-for="(rank, index) in ranks"
              :key="index"
              class="mt-4 px-4 py-4 bg-white mx-auto rounded"
              :class="isMyRank(rank) && 'border border-green-500'"
            >
              <div class="mb-2 flex items-center">
                <img
                  class="w-10 h-10 rounded-full mr-4"
                  :src="`https://ui-avatars.com/api/?name=${
                    index + 1
                  }&color=7F9CF5&background=EBF4FF`"
                />
                <div>
                  <div class="font-bold">{{ rank.name }}</div>
                  <div class="flex">
                    <chip-label :bgColor="chipColor(rank.type)">
                      <span class="text-xs">{{ rank.type }}</span>
                    </chip-label>
                    <chip-label :bgColor="'bg-green-500'" class="flex ml-2">
                      <dollar-icon />
                      <span class="ml-1">Total Poin:</span>
                      <span class="ml-1">{{ rank.total_point || 0 }}</span>
                    </chip-label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "../../Layouts/AppLayout";
import DollarIcon from "../../Icons/Dollar";
import ChipLabel from "../../Jetstream/ChipLabel";
import CardLoader from "../../Jetstream/CardLoader";
import { formatRupiah } from "../../helpers";

export default {
  components: {
    AppLayout,
    DollarIcon,
    ChipLabel,
    CardLoader,
  },

  data() {
    return {
      ranks: [],
      total_point: 0,
      total_member: 0,
      multiplier: null,
      filter: "Semua",
      is_fetching: false,
    };
  },

  mounted() {
    this.fetchRanks();
  },

  computed: {
    getFilter() {
      return this.$page.user.type === "ADMIN"
        ? this.filter
        : this.$page.user.type.toUpperCase();
    },
  },

  methods: {
    formatRupiah,
    chipColor(type) {
      switch (type) {
        case "AGENT":
          return "bg-purple-500";

        case "RESELLER":
          return "bg-blue-500";

        case "ADMIN":
          return "bg-green-500";

        default:
          return "";
      }
    },
    changeFilter($event) {
      this.filter = $event.target.value;
      this.fetchRanks();
    },
    isMyRank(rank) {
      if (rank.user_id === this.$page.user.id) {
        return true;
      }
      return false;
    },
    async fetchRanks() {
      this.is_fetching = true;
      try {
        const {
          data: { data, total_point, total_member, multiplier },
        } = await axios.get("/api/rank", {
          params: {
            filter: this.getFilter,
          },
        });
        this.ranks = data;
        this.total_point = total_point;
        this.total_member = total_member;
        this.multiplier = multiplier;

        this.is_fetching = false;
      } catch (e) {
        console.log(e);
        this.is_fetching = false;
      }
    },
  },
};
</script>
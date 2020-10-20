<template>
  <app-layout>
    <template #header>
      <h2
        class="flex justify-between items-center font-semibold text-xl text-gray-800 leading-tight"
      >
        Klaim Poin
        <inertia-link :href="route('admin.claim-points-view')">
          <button class="bg-green-500 text-white px-2 py-1 rounded">
            <plus-icon />
          </button>
        </inertia-link>
      </h2>
    </template>

    <div class="flex" v-if="$page.user.type === 'ADMIN'">
      <div class="bg-white pl-4 py-3 border-t">Filter:</div>
      <select
        v-model="filter"
        @change="changeFilter($event)"
        class="form-input focus-none w-full rounded border-0 border-t"
      >
        <option>Semua</option>
        <option>Agen</option>
        <option>Reseller</option>
      </select>
    </div>

    <div class="pt-6 px-3" v-if="$page.user.type === 'ADMIN'">
      <input
        v-model="search"
        @input="changeSearch($event)"
        class="shadow w-full text-gray-700 py-3 px-3 leading-tight focus:outline-none"
        type="text"
        placeholder="Cari dengan nama atau email"
        aria-label="Pencarian"
      />
    </div>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden sm:rounded-lg">
          <div v-if="is_fetching" class="max-w-sm w-full lg:max-w-full lg:flex">
            <card-loader :total="2" />
          </div>
          <div v-else>
            <div v-for="point in points" :key="point.id" class="my-4">
              <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
                <a :href="point.payment_file_url" target="_blank">
                  <img class="w-full" :src="point.payment_file_url" />
                </a>
                <div class="px-6 py-4">
                  <div class="font-bold text-xl mb-2">
                    {{ point.user.name }}
                  </div>
                  <chip-label :bgColor="chipColor(point.user.type)"
                    >ID: {{ point.user_detail.identifier }}
                  </chip-label>
                  <p class="mt-3">{{ point.user.email }}</p>
                  <p class="text-sm">
                    {{ point.created_at | luxon:format('E LLLL y, HH:mm:ss') }}
                  </p>
                </div>
                <div class="px-6 pb-2 flex justify-between">
                  <span
                    class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2"
                    >Total Pcs: {{ point.total_pcs }}</span
                  >
                  <div>
                    <div class="flex">
                      <button
                        @click="verifyPoint(point.id, true)"
                        class="inline-flex items-center px-2 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition ease-in-out duration-150"
                      >
                        <check-mark-icon />
                      </button>
                      <button
                        @click="verifyPoint(point.id, false)"
                        class="inline-flex items-center px-2 py-1 ml-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 transition ease-in-out duration-150"
                      >
                        <cross-mark-icon />
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <div v-if="point.status === 'SEDANG DIVERIFIKASI'" class="flex">
                  <button
                    @click="verifyAgent(point.id, true)"
                    class="inline-flex items-center px-2 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition ease-in-out duration-150"
                  >
                    <check-mark-icon />
                  </button>
                  <button
                    @click="verifyAgent(point.id, false)"
                    class="inline-flex items-center px-2 py-1 ml-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 transition ease-in-out duration-150"
                  >
                    <cross-mark-icon />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <no-data :isShow="!is_fetching && points.length === 0" />
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import JetButton from "./../../JetStream/Button";
import UserStatus from "./../../JetStream/UserStatus";
import ChipLabel from "./../../JetStream/ChipLabel";
import NoData from "./../../JetStream/NoData";
import CardLoader from "./../../Jetstream/CardLoader";
import WhatsappIcon from "./../../Icons/Whatsapp";
import CrossMarkIcon from "./../../Icons/CrossMark";
import CheckMarkIcon from "./../../Icons/CheckMark";
import ShopeeIcon from "./../../Icons/Shopee";
import InstagramIcon from "./../../Icons/Instagram";
import CardIcon from "./../../Icons/Card";
import DollarIcon from "./../../Icons/Dollar";
import PlusIcon from "./../../Icons/Plus";

export default {
  components: {
    AppLayout,
    UserStatus,
    JetButton,
    ChipLabel,
    NoData,
    CardLoader,
    WhatsappIcon,
    CrossMarkIcon,
    CheckMarkIcon,
    ShopeeIcon,
    InstagramIcon,
    CardIcon,
    DollarIcon,
    PlusIcon,
  },

  data() {
    return {
      filter: "Semua",
      points: [],
      is_fetching: true,
      search: "",
    };
  },

  mounted() {
    this.fetchPoints();
  },

  methods: {
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
      this.fetchPoints();
    },
    changeSearch($event) {
      this.search = $event.target.value;
      this.status = "Semua";
      this.fetchPoints();
    },
    async fetchPoints() {
      try {
        this.is_fetching = true;
        const {
          data: { data },
        } = await axios.get("/api/point", {
          params: { dxpoint: this.$page.user.id },
        });
        this.points = data;
        this.is_fetching = false;
      } catch (e) {
        console.log(e);
        this.is_fetching = false;
      }
    },
  },
};
</script>
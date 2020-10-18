<template>
  <app-layout>
    <template #header>
      <h2
        class="flex justify-between items-center font-semibold text-xl text-gray-800 leading-tight"
      >
        Klaim dan Poin
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
          <div v-else class="max-w-sm w-full md:max-w-full md:flex">
            <div
              v-for="point in points"
              :key="point.id"
              class="bg-white mb-6 md:ml-6 shadow rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal"
            >
              <div class="flex justify-between items-center mb-4">
                <span
                  class="flex rounded-full py-1 px-2 bg-green-500 text-white"
                >
                  <dollar-icon />
                  <span class="mx-1">{{ point.total_pcs }}</span>
                </span>
              </div>
              <hr class="mb-3" />
              <div class="flex items-center mb-4">
                <img
                  v-if="!point.profile_photo_path"
                  class="w-10 h-10 rounded-full mr-4"
                  :src="`https://ui-avatars.com/api/?name=${point.name}&color=7F9CF5&background=EBF4FF`"
                  alt="Avatar"
                />
                <img
                  v-if="point.profile_photo_path"
                  class="w-10 h-10 rounded-full mr-4"
                  :src="`${base_url}/storage/${point.profile_photo_path}`"
                  alt="Avatar"
                />
                <div class="text-sm">
                  <p class="text-gray-900 leading-none">{{ point.name }}</p>
                  <p class="text-gray-600">{{ point.email }}</p>
                  <chip-label :bgColor="'bg-purple-500'">
                    ID Agen: {{ point.identifier }}
                  </chip-label>
                </div>
              </div>

              <div class="mb-5 mt-1 flex">
                <chip-label :bgColor="'bg-gray-800'" class="ml-2">
                  <a :href="point.payment_file_url" target="_blank"
                    >Bukti Pembayaran</a
                  >
                </chip-label>
              </div>
              <hr class="my-3" />
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
        } = await axios.get("/api/point");
        console.log(data);
        this.is_fetching = false;
      } catch (e) {
        console.log(e);
        this.is_fetching = false;
      }
    },
  },
};
</script>
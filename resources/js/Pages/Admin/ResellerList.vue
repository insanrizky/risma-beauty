<template>
  <app-layout>
    <template #header>
      <h2
        class="flex items-baseline font-semibold text-xl text-gray-800 leading-tight flex items-center"
      >
        <span>Reseller</span>
        <span class="text-sm ml-2">dari Agen: {{ $page.agent_name }}</span>
      </h2>
    </template>

    <div class="bg-white border-t">
      <div class="flex max-w-7xl mx-auto sm:px-4 px-0">
        <div class="bg-white pl-4 py-3 border-t">Filter:</div>
        <select
          v-model="filter"
          @change="changeFilter($event)"
          class="form-input focus-none w-full rounded border-0 border-t"
        >
          <option>Semua</option>
          <option>Sedang Diverifikasi</option>
          <option>Aktif</option>
          <option>Gagal Verifikasi</option>
        </select>
      </div>
    </div>

    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden sm:rounded-lg">
          <div class="px-4 py-2 mt-2 mb-0 bg-green-500 text-white rounded">
            <div class="font-bold text-xl">Total Poin: {{ total_point }}</div>
            <div class="font-bold">
              Total Reseller: {{ total_member || 0 }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto pt-6 sm:px-8 px-4">
      <input
        v-model="search"
        @input="changeSearch($event)"
        class="shadow w-full text-gray-700 py-3 px-3 leading-tight focus:outline-none"
        type="text"
        placeholder="Cari Reseller"
        aria-label="Reseller"
      />
    </div>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="overflow-hidden sm:rounded-lg">
          <div v-if="is_fetching">
            <card-loader :total="3" />
          </div>
          <div v-else class="max-w-sm w-full md:max-w-full md:flex">
            <div
              v-for="reseller in resellers"
              :key="reseller.id"
              class="bg-white mb-6 md:ml-6 shadow rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal"
            >
              <div class="flex justify-between items-center mb-4">
                <user-status :status="reseller.status" class="text-sm" />
                <span
                  v-if="reseller.status === 'AKTIF'"
                  class="flex rounded py-1 px-2 bg-green-500 text-white items-center"
                >
                  <coin-icon />
                  <span class="ml-2 mr-1 text-sm">{{
                    reseller.total_point || 0
                  }}</span>
                </span>
              </div>
              <hr class="mb-3" />
              <div class="flex items-center mb-4">
                <img
                  v-if="!reseller.profile_photo_path"
                  class="w-10 h-10 rounded-full mr-4"
                  :src="`https://ui-avatars.com/api/?name=${reseller.name}&color=7F9CF5&background=EBF4FF`"
                  alt="Avatar"
                />
                <img
                  v-if="reseller.profile_photo_path"
                  class="w-10 h-10 rounded-full mr-4"
                  :src="`${base_url}${reseller.profile_photo_path}`"
                  alt="Avatar"
                />
                <div class="text-sm">
                  <p class="text-gray-900 leading-none">{{ reseller.name }}</p>
                  <p class="text-gray-600">{{ reseller.email }}</p>
                </div>
              </div>
              <div class="mb-4">
                <div class="flex mb-4" v-if="reseller.status === 'AKTIF'">
                  <chip-label :bgColor="chipIdColor(reseller.type)">
                    ID Reseller: {{ reseller.identifier }}
                  </chip-label>
                </div>
                <div class="flex">
                  <whatsapp-icon />
                  <p class="ml-2 text-gray-700 text-base">
                    {{ reseller.contact || "-" }}
                  </p>
                </div>
                <div class="flex">
                  <card-icon />
                  <p class="ml-2 text-gray-700 text-base">
                    Bank {{ getBankName(reseller) }} -
                    {{ reseller.account_number }}
                  </p>
                </div>
                <p class="mt-3 text-gray-700 text-base">
                  {{ getFullAddress(reseller) }}
                </p>
              </div>

              <div class="mb-5 mt-1 flex">
                <chip-label :bgColor="'bg-gray-800'">
                  <a :href="reseller.ktp_file_url" target="_blank">KTP</a>
                </chip-label>
                <chip-label :bgColor="'bg-gray-800'" class="ml-2">
                  <a :href="reseller.payment_file_url" target="_blank"
                    >Bukti Pembayaran</a
                  >
                </chip-label>
              </div>
              <hr class="my-3" />
              <div class="flex justify-between">
                <div class="flex">
                  <a :href="reseller.instagram_link" target="_blank">
                    <instagram-icon />
                  </a>
                  <a :href="reseller.shopee_link" target="_blank" class="ml-2">
                    <shopee-icon />
                  </a>
                </div>

                <div
                  v-if="reseller.status === 'SEDANG DIVERIFIKASI'"
                  class="flex"
                >
                  <button
                    @click="verifyReseller(reseller.id, true)"
                    class="inline-flex items-center px-2 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition ease-in-out duration-150"
                  >
                    <check-mark-icon />
                  </button>
                  <button
                    @click="verifyReseller(reseller.id, false)"
                    class="inline-flex items-center px-2 py-1 ml-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 transition ease-in-out duration-150"
                  >
                    <cross-mark-icon />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mb-4">
          <no-data :isShow="!is_fetching && resellers.length === 0" />
        </div>
        <pagination
          v-if="resellers.length > 0"
          :pagination="pagination"
          @event="fetchResellers"
        />
      </div>
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
import CoinIcon from "./../../Icons/Coin";
import { formatRupiah } from "./../../helpers";
import Pagination from "./../../JetStream/Pagination";

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
    CoinIcon,
    Pagination,
  },

  data() {
    return {
      base_url: "",
      resellers: [],
      filter: "Semua",
      is_fetching: false,
      search: "",
      total_member: 0,
      total_point: 0,
      pagination: {},
    };
  },

  mounted() {
    this.fetchResellers();
  },

  methods: {
    formatRupiah,
    chipIdColor(type) {
      switch (type) {
        case "AGENT":
          return "bg-purple-500";
        case "RESELLER":
          return "bg-blue-500";
        default:
          return "bg-green-500";
      }
    },
    getFullAddress(reseller) {
      return `${reseller.address || "-"}, ${
        reseller.city ? reseller.city.name : "-"
      }, ${reseller.province ? reseller.province.name : "-"}`;
    },
    getBankName(reseller) {
      if (!reseller.bank) {
        return "-";
      }
      return reseller.bank.name;
    },
    changeSearch($event) {
      this.search = $event.target.value;
      this.status = "Semua";
      this.fetchResellers();
    },
    changeFilter($event) {
      this.filter = $event.target.value;
      this.fetchResellers();
    },
    async fetchResellers(page = 1) {
      this.is_fetching = true;
      try {
        const {
          data: {
            data,
            meta: { base_url, total_point, pagination },
          },
        } = await axios.get("/api/admin/user", {
          params: {
            type: "RESELLER",
            status:
              this.filter === "Semua" ? undefined : this.filter.toUpperCase(),
            search: this.search,
            page,
            upline: this.$page.identifier,
          },
        });
        this.base_url = base_url;
        this.resellers = data;
        this.total_member = pagination.total;
        this.total_point = total_point;
        this.pagination = pagination;

        this.is_fetching = false;
      } catch (e) {
        console.log(e);
        this.is_fetching = false;
      }
    },
    async verifyReseller(id, is_verified) {
      try {
        const {
          data: { data },
        } = await axios.put(`/api/admin/verify/${id}`, {
          is_verified,
        });
        const reseller = this.resellers.find((reseller) => reseller.id === id);
        reseller.status = data.status;
        reseller.identifier = data.identifier;
        if (is_verified) {
          this.$swal("Berhasil!", "Agen berhasil diverifikasi", "success");
        } else {
          this.$swal("Berhasil!", "Verifikasi Agen digagalkan", "success");
        }
      } catch (e) {
        this.$swal("Terjadi Kesalahan!", "", "error");
        console.log(e);
      }
    },
  },
};
</script>
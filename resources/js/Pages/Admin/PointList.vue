<template>
  <app-layout>
    <template #header>
      <h2
        class="flex justify-between items-center font-semibold text-xl text-gray-800 leading-tight"
      >
        Klaim Poin
        <inertia-link
          :href="route('admin.claim-points-view')"
          v-if="$page.user.type !== 'ADMIN'"
        >
          <button class="bg-green-500 text-white px-2 py-1 rounded">
            <plus-icon />
          </button>
        </inertia-link>
      </h2>
    </template>

    <div class="bg-white border-t">
      <div
        class="flex max-w-7xl mx-auto sm:px-4 px-0"
        v-if="$page.user.type === 'ADMIN'"
      >
        <div class="bg-white pl-4 py-3">Tipe:</div>
        <select
          v-model="filterUserType"
          @change="changeUserType($event)"
          class="form-input focus-none w-full rounded border-0"
        >
          <option>Semua</option>
          <option>Agent</option>
          <option>Reseller</option>
        </select>
      </div>
    </div>
    <div class="bg-white">
      <div
        class="flex max-w-7xl mx-auto sm:px-4 px-0"
      >
        <div class="bg-white pl-4 py-3">Filter:</div>
        <select
          v-model="filterStatus"
          @change="changeFilterStatus($event)"
          class="form-input focus-none w-full rounded border-0"
        >
          <option>Semua</option>
          <option>Menunggu Diverifikasi</option>
          <option>Klaim Diterima</option>
          <option>Sudah Dicairkan</option>
          <option>Gagal Verifikasi</option>
        </select>
      </div>
    </div>

    <div
      class="max-w-7xl mx-auto pt-6 sm:px-8 px-4"
      v-if="$page.user.type === 'ADMIN'"
    >
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
          <div v-if="is_fetching">
            <card-loader :total="3" />
          </div>
          <div v-else class="grid sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="point in points"
              :key="point.id"
              class="my-4 sm:mr-2"
              :class="{ 'mx-auto': points.length > 2 }"
            >
              <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
                <a :href="point.payment_file_url" target="_blank">
                  <img class="w-full" :src="point.payment_file_url" />
                </a>
                <div class="px-6 py-4">
                  <div class="font-bold text-xl mb-2">
                    {{ point.name }}
                  </div>
                  <chip-label :bgColor="chipColor(point.type)"
                    >ID: {{ point.identifier }}
                  </chip-label>
                  <chip-label :bgColor="'bg-green-500'" class="mt-2 flex items-center">
                    <dollar-icon />
                    <span class="ml-1">
                      Nominal Klaim: {{ formatRupiah(point.amount) }}
                    </span>
                  </chip-label>
                  <p class="mt-3">{{ point.email }}</p>
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
                    <div
                      class="flex"
                      v-if="
                        canVerifyPoint(
                          point.type,
                          point.upline_identifier,
                          point.status
                        )
                      "
                    >
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
                    <div v-if="canDeleteClaim(point.user_id, point.status)">
                      <button
                        @click="deleteClaim(point.id)"
                        class="inline-flex items-center px-2 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition ease-in-out duration-150"
                      >
                        <trash-icon />
                      </button>
                    </div>
                    <div v-if="point.status !== 'MENUNGGU DIVERIFIKASI'">
                      <chip-label :bgColor="chipClaimStatus(point.status)">{{
                        point.status
                      }}</chip-label>
                    </div>
                  </div>
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
import CrossMarkIcon from "./../../Icons/CrossMark";
import CheckMarkIcon from "./../../Icons/CheckMark";
import CardIcon from "./../../Icons/Card";
import DollarIcon from "./../../Icons/Dollar";
import PlusIcon from "./../../Icons/Plus";
import TrashIcon from "./../../Icons/Trash";

export default {
  components: {
    AppLayout,
    UserStatus,
    JetButton,
    ChipLabel,
    NoData,
    CardLoader,
    CrossMarkIcon,
    CheckMarkIcon,
    CardIcon,
    DollarIcon,
    PlusIcon,
    TrashIcon,
  },

  data() {
    return {
      debounceSearch: null,
      filterUserType: "Semua",
      filterStatus: "Semua",
      points: [],
      is_fetching: true,
      search: "",
    };
  },

  mounted() {
    this.fetchPoints();
  },

  methods: {
    canVerifyPoint(targetType, uplineIdentifier, status) {
      const {
        user: { type },
        user_detail: { identifier },
      } = this.$page;
      return (
        (type === "ADMIN" && status === "MENUNGGU DIVERIFIKASI") ||
        (targetType === "RESELLER" && uplineIdentifier === identifier)
      );
    },
    canDeleteClaim(targetUserId, status) {
      return (
        targetUserId === this.$page.user.id && status === "MENUNGGU DIVERIFIKASI"
      );
    },
    chipClaimStatus(status) {
      switch (status) {
        case "MENUNGGU DIVERIFIKASI":
          return "bg-orange-500";
        case "KLAIM DITERIMA":
          return "bg-green-500";
        case "GAGAL VERIFIKASI":
          return "bg-red-600";
        case "KLAIM DICAIRKAN":
          return "bg-blue-500";
        default:
          return "bg-black";
      }
    },
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
    changeUserType($event) {
      this.filterUserType = $event.target.value;
      this.fetchPoints();
    },
    changeFilterStatus($event) {
      this.filterStatus = $event.target.value;
      this.fetchPoints();
    },
    changeSearch($event) {
      this.search = $event.target.value;
      clearTimeout(this.debounceSearch);
      this.debounceSearch = setTimeout(() => {
        this.fetchPoints();
      }, 700);
    },
    async deleteClaim(id) {
      try {
        const {
          data: { data },
        } = await axios.delete(`/api/point/${id}`);
        this.fetchPoints();
        this.$swal("Berhasil!", "Klaim telah terhapus", "success");
      } catch (e) {
        console.log(e);
        this.$swal("Terjadi Kesalahan!", "", "error");
      }
    },
    async verifyPoint(id, is_verified) {
      try {
        const {
          data: { data },
        } = await axios.put(`/api/point/verify/${id}`, {
          is_verified,
        });

        this.fetchPoints();
        this.$swal("Berhasil!", "Klaim berhasil diverifikasi", "success");
      } catch (e) {
        console.log(e);
        this.$swal("Terjadi Kesalahan!", "", "error");
      }
    },
    async fetchPoints() {
      try {
        this.is_fetching = true;
        const {
          data: { data },
        } = await axios.get("/api/point", {
          params: {
            dxpoint:
              this.$page.user.type === "ADMIN" ? undefined : this.$page.user.id,
            type: this.filterUserType,
            status: this.filterStatus,
            search: this.search,
          },
        });
        this.points = data;
        this.is_fetching = false;
      } catch (e) {
        console.log(e);
        this.is_fetching = false;
      }
    },
    formatRupiah(number) {
      if (number === null) {
        number = 0;
      }

      let number_string = number.toString(),
        sisa = number_string.length % 3,
        rupiah = number_string.substr(0, sisa),
        ribuan = number_string.substr(sisa).match(/\d{3}/g);

      if (ribuan) {
        const separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
      }
      return `Rp ${rupiah}`;
    },
  },
};
</script>
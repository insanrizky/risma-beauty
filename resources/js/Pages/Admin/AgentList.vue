<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agen</h2>
    </template>

    <div class="bg-white border-t">
      <div class="flex max-w-7xl mx-auto sm:px-4 px-0">
        <div class="bg-white pl-4 py-3">Filter:</div>
        <select
          v-model="filter"
          @change="changeFilter($event)"
          class="form-input focus-none w-full rounded border-0"
        >
          <option>Semua</option>
          <option>Sedang Diverifikasi</option>
          <option>Aktif</option>
          <option>Gagal Verifikasi</option>
          <option>Akun Dinonaktifkan</option>
        </select>
      </div>
    </div>

    <div class="max-w-7xl mx-auto pt-6 sm:px-8 px-4">
      <input
        v-model="search"
        @input="changeSearch($event)"
        class="shadow w-full text-gray-700 py-3 px-3 leading-tight focus:outline-none"
        type="text"
        placeholder="Cari Agen"
        aria-label="Agen"
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
              v-for="agent in agents"
              :key="agent.id"
              class="bg-white mb-6 md:mr-6 shadow rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal"
              :class="{ 'mx-auto': agents.length > 2 }"
            >
              <div class="flex justify-between items-center mb-4">
                <user-status :status="agent.status" class="text-sm" />
                <chip-label
                  v-if="agent.status === 'AKTIF'"
                  :bgColor="pointComparisonColor(agent)"
                  class="flex items-center"
                >
                  <coin-icon />
                  <span class="ml-2 text-sm"
                    >{{ agent.total_point_reseller || 0 }} /
                    {{ agent.total_point || 0 }}</span
                  >
                </chip-label>
              </div>
              <hr class="mb-3" />
              <div class="flex items-center mb-4">
                <img
                  v-if="!agent.profile_photo_path"
                  class="w-10 h-10 rounded-full mr-4"
                  :src="`https://ui-avatars.com/api/?name=${agent.name}&color=7F9CF5&background=EBF4FF`"
                  alt="Avatar"
                />
                <img
                  v-if="agent.profile_photo_path"
                  class="w-10 h-10 rounded-full mr-4"
                  :src="`${base_url}/storage/${agent.profile_photo_path}`"
                  alt="Avatar"
                />
                <div class="text-sm">
                  <p class="text-gray-900 leading-none">{{ agent.name }}</p>
                  <p class="text-gray-600">{{ agent.email }}</p>
                </div>
              </div>
              <div class="mb-4">
                <div class="flex mb-4" v-if="agent.status === 'AKTIF'">
                  <chip-label :bgColor="'bg-purple-500'">
                    ID Agen: {{ agent.identifier }}
                  </chip-label>
                </div>
                <div class="flex">
                  <whatsapp-icon />
                  <p class="ml-2 text-gray-700 text-base">
                    {{ agent.contact }}
                  </p>
                </div>
                <div class="flex">
                  <card-icon />
                  <p class="ml-2 text-gray-700 text-base">
                    Bank {{ agent.bank.name }} - {{ agent.account_number }}
                  </p>
                </div>
                <p class="mt-3 text-gray-700 text-base">
                  {{ getFullAddress(agent) }}
                </p>
              </div>

              <div class="mb-5 mt-1 flex">
                <chip-label :bgColor="'bg-gray-800'">
                  <a :href="agent.ktp_file_url" target="_blank">KTP</a>
                </chip-label>
                <chip-label :bgColor="'bg-gray-800'" class="ml-2">
                  <a :href="agent.payment_file_url" target="_blank"
                    >Bukti Pembayaran</a
                  >
                </chip-label>
              </div>
              <hr class="my-3" />
              <div class="flex justify-between">
                <div class="flex">
                  <a :href="agent.instagram_link" target="_blank">
                    <instagram-icon />
                  </a>
                  <a
                    v-if="agent.shopee_link"
                    :href="agent.shopee_link"
                    target="_blank"
                    class="ml-2"
                  >
                    <shopee-icon />
                  </a>
                </div>

                <div v-if="agent.status === 'SEDANG DIVERIFIKASI'" class="flex">
                  <button
                    @click="confirmVerifyAgent(agent, true)"
                    class="inline-flex items-center px-2 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition ease-in-out duration-150"
                  >
                    <check-mark-icon />
                  </button>
                  <button
                    @click="confirmVerifyAgent(agent, false)"
                    class="inline-flex items-center px-2 py-1 ml-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 transition ease-in-out duration-150"
                  >
                    <cross-mark-icon />
                  </button>
                </div>

                <div
                  v-if="
                    agent.status === 'AKTIF' ||
                    agent.status === 'AKUN DINONAKTIFKAN'
                  "
                  class="flex items-center"
                >
                  <inertia-link
                    :href="
                      route('admin.show-resellers', {
                        identifier: agent.identifier,
                      })
                    "
                  >
                    <jet-secondary-button>Lihat Reseller</jet-secondary-button>
                  </inertia-link>
                  <button
                    v-if="agent.status === 'AKTIF'"
                    @click="confirmSuspend(agent.user_id, agent.name)"
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
          <no-data :isShow="!is_fetching && agents.length === 0" />
        </div>

        <pagination
          v-if="agents.length > 0"
          :pagination="pagination"
          @event="fetchAgents"
        />
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import JetButton from "./../../JetStream/Button";
import JetSecondaryButton from "./../../JetStream/SecondaryButton";
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
import Pagination from "./../../JetStream/Pagination";

export default {
  components: {
    AppLayout,
    UserStatus,
    JetButton,
    JetSecondaryButton,
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
      debounceSearch: null,
      base_url: "",
      agents: [],
      filter: "Semua",
      is_fetching: false,
      search: "",
      pagination: {},
    };
  },

  mounted() {
    this.fetchAgents();
  },

  methods: {
    pointComparisonColor(agent) {
      const { total_point, total_point_reseller } = agent;
      if (total_point_reseller > total_point) {
        return "bg-orange-500";
      }

      return "bg-green-500";
    },
    getFullAddress(agent) {
      return `${agent.address}, ${agent.city.name}, ${agent.province.name}`;
    },
    changeSearch($event) {
      this.search = $event.target.value;
      this.status = "Semua";

      clearTimeout(this.debounceSearch);
      this.debounceSearch = setTimeout(() => {
        this.fetchAgents();
      }, 700);
    },
    changeFilter($event) {
      this.filter = $event.target.value;
      this.fetchAgents();
    },
    async fetchAgents(page = 1) {
      this.is_fetching = true;
      try {
        const {
          data: {
            data,
            meta: { base_url, pagination },
          },
        } = await axios.get("/api/admin/user", {
          params: {
            type: "AGENT",
            status:
              this.filter === "Semua" ? undefined : this.filter.toUpperCase(),
            search: this.search,
            page,
          },
        });
        this.base_url = base_url;
        this.agents = data;
        this.pagination = pagination;
        this.is_fetching = false;
      } catch (e) {
        console.log(e);
        this.is_fetching = false;
      }
    },
    confirmVerifyAgent(agent, is_verified) {
      let title = `Yakin ingin menolak agen ini?`;
      if (is_verified) {
        title = `Yakin ingin mengaktifkan agen ini?`;
      }

      this.$swal({
        title,
        text: `${agent.name} - ${agent.email}`,
        showDenyButton: true,
        confirmButtonText: "Ya",
        denyButtonText: `Batalkan`,
      }).then((result) => {
        if (result.isConfirmed) {
          this.verifyAgent(agent.id, is_verified);
        }
      });
    },
    async verifyAgent(id, is_verified) {
      try {
        const {
          data: { data },
        } = await axios.put(`/api/admin/verify/${id}`, {
          is_verified,
        });
        const agent = this.agents.find((agent) => agent.user_id === id);
        agent.status = data.status;
        agent.identifier = data.identifier;
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
    confirmSuspend(id, name) {
      this.$swal({
        title: `Yakin ingin menonaktifkan Agen ${name}?`,
        text: 'Semua Reseller di bawah Agen ini akan otomatis dinonaktifkan juga.',
        showDenyButton: true,
        confirmButtonText: "Ya",
        denyButtonText: `Batalkan`,
      }).then((result) => {
        if (result.isConfirmed) {
          this.suspend(id);
        }
      });
    },
    async suspend(id) {
      try {
        await axios.put(`/api/admin/suspend/${id}`);
        this.$swal("Berhasil!", "Akun berhasil dinonaktifkan", "success");
        this.fetchAgents();
      } catch (e) {
        this.$swal("Terjadi Kesalahan!", "", "error");
        console.log(e);
      }
    },
  },
};
</script>


<style type="text/css">
.focus-none:focus {
  box-shadow: unset;
}
</style>
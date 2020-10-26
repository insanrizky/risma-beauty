<template>
  <div>
    <div class="p-8 sm:px-20 bg-white border-b border-gray-200">
      <!-- <div class="mb-8">
                <jet-application-logo class="block h-12 w-auto" />
            </div> -->

      <div class="text-2xl">Selamat Datang, {{ $page.user.name }}! 👋🏻</div>

      <div class="mt-6 text-gray-500">
        <p class="mb-2">Status Akun Kamu:</p>
        <div class="flex">
          <user-status :status="$page.data.status" />
          <chip-label
            v-if="$page.data.status === 'AKTIF'"
            :bgColor="'bg-green-500'"
            class="ml-2"
          >
            Poin: {{ $page.data.total_point || 0 }}
          </chip-label>
          <chip-label
            v-if="$page.data.status === 'AKTIF'"
            :bgColor="'bg-green-500'"
            class="ml-2"
          >
            Nominal: {{ formatRupiah($page.data.total_amount) || 0 }}
          </chip-label>
        </div>

        <div class="mt-4">
          <chip-label
            v-if="$page.data.status === 'AKTIF'"
            :bgColor="chipIdColor"
            class="mb-2"
            >ID Kamu: {{ $page.data.identifier }}</chip-label
          >
        </div>

        <!-- <div class="mt-2">
          <chip-label
            v-if="$page.user.type === 'RESELLER'"
            :bgColor="'bg-purple-500'"
            >ID Agen: {{ $page.data.upline_identifier }}</chip-label
          >
        </div> -->

        <p class="mt-6 border-t pt-4" v-if="isInRegistration">
          Lengkapi dulu data kamu supaya bisa diverifikasi admin dan mulai
          jualan! 😉
        </p>

        <p class="mt-6 border-t pt-4" v-if="isInVerification">
          Mohon bersabar ya! Insya Allah admin bakal verifikasi akun kamu
          secepatnya kok! 😉
        </p>

        <p class="mt-6 border-t pt-4" v-if="isActive">
          Yeay! 🎉<br />Akun kamu sudah aktif. Kamu bisa jualan sekarang! 💰💰💰
        </p>

        <p class="mt-6 border-t pt-4" v-if="isVerificatonFailed">
          Yah.. 😭<br />Kamu belum bisa jualan nih! Tapi kamu bisa hubungin
          admin kok untuk informasi lebih lanjut.
        </p>
      </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
      <div class="p-6">
        <div class="flex items-center">
          <profile-icon />
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
            <inertia-link :href="route('profile.show-detail')"
              >Profil Saya</inertia-link
            >
          </div>
        </div>

        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Profil kamu harus lengkap dan sesuai dengan data yang benar ya! No
            tipu-tipu! ⛔️
          </div>

          <inertia-link :href="route('profile.show-detail')">
            <div
              class="mt-3 flex items-center text-sm font-semibold text-indigo-700"
            >
              <div>Perbarui Profil Saya</div>

              <div class="ml-1 text-indigo-500">
                <arrow-right-icon />
              </div>
            </div>
          </inertia-link>
        </div>
      </div>

      <div
        v-if="isActive"
        class="p-6 border-t border-gray-200 md:border-t-0 md:border-l"
      >
        <div class="flex items-center">
          <coin-icon width="32" height="32" />
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
            <inertia-link :href="route('admin.show-points')"
              >Klaim dan Tukar Poin</inertia-link
            >
          </div>
        </div>

        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Ayo klaim penjualan kamu sekarang dan dapatkan poin
            sebanyak-banyaknya!
          </div>

          <inertia-link :href="route('admin.show-points')">
            <div
              class="mt-3 flex items-center text-sm font-semibold text-indigo-700"
            >
              <div>Klaim Sekarang!</div>

              <div class="ml-1 text-indigo-500">
                <arrow-right-icon />
              </div>
            </div>
          </inertia-link>
        </div>
      </div>

      <div v-if="isActive && $page.user.type === 'AGENT'" class="p-6">
        <div class="flex items-center">
          <agent-search-icon />
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
            <inertia-link
              :href="
                route('admin.show-resellers', {
                  identifier: $page.data.identifier,
                })
              "
              >Reseller Saya</inertia-link
            >
          </div>
        </div>

        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Kamu bisa lihat semua daftar Reseller kamu di sini lho!
          </div>

          <inertia-link
            :href="
              route('admin.show-resellers', {
                identifier: $page.data.identifier,
              })
            "
          >
            <div
              class="mt-3 flex items-center text-sm font-semibold text-indigo-700"
            >
              <div>Lihat Reseller Saya</div>

              <div class="ml-1 text-indigo-500">
                <arrow-right-icon />
              </div>
            </div>
          </inertia-link>
        </div>
      </div>

      <div v-if="isActive" class="p-6">
        <div class="flex items-center">
          <rank-icon />
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
            <inertia-link :href="route('admin.show-ranks')"
              >Peringkat</inertia-link
            >
          </div>
        </div>

        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Kamu bisa lihat peringkat kamu di antara agen-agen yang lain lho!
          </div>

          <inertia-link :href="route('admin.show-ranks')">
            <div
              class="mt-3 flex items-center text-sm font-semibold text-indigo-700"
            >
              <div>Lihat Peringkat Saya</div>

              <div class="ml-1 text-indigo-500">
                <arrow-right-icon />
              </div>
            </div>
          </inertia-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import JetApplicationLogo from "./../Jetstream/ApplicationLogo";
import UserStatus from "./../Jetstream/UserStatus";
import ChipLabel from "./../Jetstream/ChipLabel";
import ProfileIcon from "./../Icons/Profile";
import CoinIcon from "./../Icons/Coin";
import AgentSearchIcon from "./../Icons/AgentSearch";
import ArrowRightIcon from "./../Icons/ArrowRight";
import RankIcon from "./../Icons/Rank";
import { formatRupiah } from "./../helpers";

export default {
  components: {
    JetApplicationLogo,
    UserStatus,
    ChipLabel,
    ProfileIcon,
    CoinIcon,
    AgentSearchIcon,
    ArrowRightIcon,
    RankIcon,
  },

  computed: {
    chipIdColor() {
      switch (this.$page.user.type) {
        case "AGENT":
          return "bg-purple-500";
        case "RESELLER":
          return "bg-blue-500";
        default:
          return "bg-green-500";
      }
    },
    isInRegistration() {
      return this.$page.data.status === "DALAM PROSES PENDAFTARAN";
    },
    isVerificatonFailed() {
      return this.$page.data.status === "GAGAL VERIFIKASI";
    },
    isActive() {
      return this.$page.data.status === "AKTIF";
    },
    isInVerification() {
      return this.$page.data.status === "SEDANG DIVERIFIKASI";
    },
  },

  methods: {
    formatRupiah,
  },
};
</script>

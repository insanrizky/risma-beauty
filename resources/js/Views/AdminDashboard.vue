<template>
  <div>
    <div class="p-8 sm:px-20 bg-white border-b border-gray-200">
      <div class="text-2xl">Selamat Datang, {{ $page.user.name }}! 👋🏻</div>

      <div class="text-gray-500">
        <p class="mb-2">Kelola bisnismu mulai dari sini!</p>

        <div class="mt-4">
          <p>Jangan lupa gabung grup Telegram kita juga ya!</p>
          <a
            href="https://t.me/joinchat/I9CH3RlTl8bUb-2yO10cxg"
            target="_blank"
            class="mt-1 flex items-center"
          >
            <telegram-icon />
            <p class="mx-2 text-l font-bold">GLOWSKIN SOAP FAMILY</p>
            <arrow-right-icon />
          </a>
          <a
            v-if="$page.user.type !== 'RESELLER'"
            href="https://t.me/joinchat/I9CH3RzKW695nPu8gjihpQ"
            target="_blank"
            class="mt-1 flex items-center"
          >
            <telegram-icon />
            <p class="mx-2 text-l font-bold">AGEN GLOWSKIN SOAP</p>
            <arrow-right-icon />
          </a>
        </div>
      </div>
    </div>

    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
      <div class="p-6">
        <div class="flex items-center">
          <agent-search-icon />
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
            <inertia-link :href="route('admin.show-agents')"
              >Agen &amp; Reseller</inertia-link
            >
          </div>
        </div>

        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Data Agen &amp; Reseller bisa ditemukan di sini!
          </div>

          <inertia-link :href="route('admin.show-agents')">
            <div
              class="mt-3 flex items-center text-sm font-semibold text-indigo-700"
            >
              <div>Lihat Agen &amp; Reseller</div>

              <div class="ml-1 text-indigo-500">
                <arrow-right-icon />
              </div>
            </div>
          </inertia-link>
        </div>
      </div>

      <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
        <div class="flex items-center">
          <coin-icon />
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
            <inertia-link :href="route('admin.show-points')"
              >Klaim dan Poin</inertia-link
            >
          </div>
        </div>

        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Agen dan Reseller mu akan klaim penjualan mereka di sini! Kamu harus
            verifikasi klaim mereka dan tukar klaim mereka dengan poin.
          </div>

          <inertia-link :href="route('admin.show-points')">
            <div
              class="mt-3 flex items-center text-sm font-semibold text-indigo-700"
            >
              <div>Verifikasi klaim</div>

              <div class="ml-1 text-indigo-500">
                <arrow-right-icon />
              </div>
            </div>
          </inertia-link>
        </div>
      </div>

      <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
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

      <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
        <div class="flex items-center">
          <setting-icon />
          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
            <inertia-link :href="route('admin.show-settings')"
              >Pengaturan</inertia-link
            >
          </div>
        </div>

        <div class="ml-12">
          <div class="mt-2 text-sm text-gray-500">
            Rekapitulasi data, pencairan klaim, dan pengaturan lanjutan ada di
            sini.
          </div>

          <inertia-link :href="route('admin.show-settings')">
            <div
              class="mt-3 flex items-center text-sm font-semibold text-indigo-700"
            >
              <div>Pengaturan Lebih Lanjut</div>

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
import AgentSearchIcon from "./../Icons/AgentSearch";
import CoinIcon from "./../Icons/Coin";
import ArrowRightIcon from "./../Icons/ArrowRight";
import RankIcon from "./../Icons/Rank";
import SettingIcon from "./../Icons/Setting";
import TelegramIcon from "./../Icons/Telegram";

export default {
  components: {
    JetApplicationLogo,
    AgentSearchIcon,
    CoinIcon,
    ArrowRightIcon,
    RankIcon,
    SettingIcon,
    TelegramIcon,
  },
};
</script>

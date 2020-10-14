<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agen</h2>
    </template>

    <div class="flex">
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
    <div class="pt-6 px-3">
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
          <div class="max-w-sm w-full lg:max-w-full lg:flex">
            <div v-if="is_fetching">
              <div
                v-for="x in [1,2]"
                :key="x"
                class="border border-gray-300 shadow rounded-md p-4 max-w-sm w-full mx-auto bg-white mb-4"
              >
                <div class="animate-pulse flex space-x-4">
                  <div class="rounded-full bg-gray-400 h-12 w-12"></div>
                  <div class="flex-1 space-y-2 py-1">
                    <div class="h-4 bg-gray-400 rounded w-4/6"></div>
                    <div class="h-4 bg-gray-400 rounded w-5/6"></div>
                  </div>
                </div>
                <div class="animate-pulse space-x-4 space-y-2 mt-4">
                  <div class="h-4 bg-gray-400 rounded w-6/6"></div>
                </div>
                <div class="animate-pulse space-x-4 space-y-2 mt-2">
                  <div class="h-4 bg-gray-400 rounded w-5/6"></div>
                </div>
              </div>
            </div>
            <div v-else>
              <div
                v-for="agent in agents"
                :key="agent.id"
                class="bg-white mb-6 md:ml-6 shadow rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal"
              >
                <div class="flex justify-between mb-4">
                  <user-status :status="agent.status" class="text-sm" />
                </div>
                <hr class="mb-3" />
                <div class="flex items-center mb-4">
                  <img
                    class="w-10 h-10 rounded-full mr-4"
                    src="https://ui-avatars.com/api/?name=Admin&color=7F9CF5&background=EBF4FF"
                    alt="Avatar of Jonathan Reinink"
                  />
                  <div class="text-sm">
                    <p class="text-gray-900 leading-none">{{ agent.name }}</p>
                    <p class="text-gray-600">{{ agent.email }}</p>
                  </div>
                </div>
                <div class="mb-4">
                  <div class="flex">
                    <svg
                      id="Bold"
                      enable-background="new 0 0 24 24"
                      height="20"
                      viewBox="0 0 24 24"
                      width="20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="m17.507 14.307-.009.075c-2.199-1.096-2.429-1.242-2.713-.816-.197.295-.771.964-.944 1.162-.175.195-.349.21-.646.075-.3-.15-1.263-.465-2.403-1.485-.888-.795-1.484-1.77-1.66-2.07-.293-.506.32-.578.878-1.634.1-.21.049-.375-.025-.524-.075-.15-.672-1.62-.922-2.206-.24-.584-.487-.51-.672-.51-.576-.05-.997-.042-1.368.344-1.614 1.774-1.207 3.604.174 5.55 2.714 3.552 4.16 4.206 6.804 5.114.714.227 1.365.195 1.88.121.574-.091 1.767-.721 2.016-1.426.255-.705.255-1.29.18-1.425-.074-.135-.27-.21-.57-.345z"
                      />
                      <path
                        d="m20.52 3.449c-7.689-7.433-20.414-2.042-20.419 8.444 0 2.096.549 4.14 1.595 5.945l-1.696 6.162 6.335-1.652c7.905 4.27 17.661-1.4 17.665-10.449 0-3.176-1.24-6.165-3.495-8.411zm1.482 8.417c-.006 7.633-8.385 12.4-15.012 8.504l-.36-.214-3.75.975 1.005-3.645-.239-.375c-4.124-6.565.614-15.145 8.426-15.145 2.654 0 5.145 1.035 7.021 2.91 1.875 1.859 2.909 4.35 2.909 6.99z"
                      />
                    </svg>
                    <p class="ml-2 text-gray-700 text-base">
                      {{ agent.contact }}
                    </p>
                  </div>
                  <div class="flex">
                    <svg
                      version="1.1"
                      id="Capa_1"
                      xmlns="http://www.w3.org/2000/svg"
                      xmlns:xlink="http://www.w3.org/1999/xlink"
                      x="0px"
                      y="0px"
                      width="20"
                      height="20"
                      viewBox="0 0 550.801 550.801"
                      style="enable-background: new 0 0 550.801 550.801"
                      xml:space="preserve"
                    >
                      <g>
                        <g>
                          <path
                            d="M0,416.798c0,13.518,10.961,24.479,24.48,24.479h501.84c13.52,0,24.48-10.961,24.48-24.479V224.911H0V416.798z
			 M289.17,377.739h205.02v27.883H289.17V377.739z M85.68,310.419l47.602,47.602L85.68,405.622l-47.601-47.602L85.68,310.419z"
                          />
                          <path
                            d="M526.32,109.524H24.48c-13.519,0-24.48,10.961-24.48,24.48v26.646h550.801v-26.646
			C550.801,120.485,539.84,109.524,526.32,109.524z"
                          />
                        </g>
                      </g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                      <g></g>
                    </svg>
                    <p class="ml-2 text-gray-700 text-base">
                      Bank {{ agent.bank_name }} - {{ agent.account_number }}
                    </p>
                  </div>
                  <p class="mt-3 text-gray-700 text-base">
                    {{ agent.address }}
                  </p>
                </div>

                <div class="flex justify-between">
                  <div class="flex">
                    <a :href="agent.instagram_link" target="_blank">
                      <svg
                        enable-background="new 0 0 24 24"
                        height="30"
                        viewBox="0 0 24 24"
                        width="30"
                        xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                      >
                        <linearGradient
                          id="SVGID_1_"
                          gradientTransform="matrix(0 -1.982 -1.844 0 -132.522 -51.077)"
                          gradientUnits="userSpaceOnUse"
                          x1="-37.106"
                          x2="-26.555"
                          y1="-72.705"
                          y2="-84.047"
                        >
                          <stop offset="0" stop-color="#fd5" />
                          <stop offset=".5" stop-color="#ff543e" />
                          <stop offset="1" stop-color="#c837ab" />
                        </linearGradient>
                        <path
                          d="m1.5 1.633c-1.886 1.959-1.5 4.04-1.5 10.362 0 5.25-.916 10.513 3.878 11.752 1.497.385 14.761.385 16.256-.002 1.996-.515 3.62-2.134 3.842-4.957.031-.394.031-13.185-.001-13.587-.236-3.007-2.087-4.74-4.526-5.091-.559-.081-.671-.105-3.539-.11-10.173.005-12.403-.448-14.41 1.633z"
                          fill="url(#SVGID_1_)"
                        />
                        <path
                          d="m11.998 3.139c-3.631 0-7.079-.323-8.396 3.057-.544 1.396-.465 3.209-.465 5.805 0 2.278-.073 4.419.465 5.804 1.314 3.382 4.79 3.058 8.394 3.058 3.477 0 7.062.362 8.395-3.058.545-1.41.465-3.196.465-5.804 0-3.462.191-5.697-1.488-7.375-1.7-1.7-3.999-1.487-7.374-1.487zm-.794 1.597c7.574-.012 8.538-.854 8.006 10.843-.189 4.137-3.339 3.683-7.211 3.683-7.06 0-7.263-.202-7.263-7.265 0-7.145.56-7.257 6.468-7.263zm5.524 1.471c-.587 0-1.063.476-1.063 1.063s.476 1.063 1.063 1.063 1.063-.476 1.063-1.063-.476-1.063-1.063-1.063zm-4.73 1.243c-2.513 0-4.55 2.038-4.55 4.551s2.037 4.55 4.55 4.55 4.549-2.037 4.549-4.55-2.036-4.551-4.549-4.551zm0 1.597c3.905 0 3.91 5.908 0 5.908-3.904 0-3.91-5.908 0-5.908z"
                          fill="#fff"
                        />
                      </svg>
                    </a>
                    <a :href="agent.shopee_link" target="_blank" class="ml-2">
                      <svg
                        version="1.1"
                        id="Layer_1"
                        xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        x="0px"
                        y="0px"
                        width="32"
                        height="32"
                        viewBox="0 0 362 535.3"
                        style="enable-background: new 0 0 362 535.3"
                        xml:space="preserve"
                      >
                        <path
                          class="st0"
                          d="M11.2,482.1c-1.6-1.1-2.1-3.3-1-5c1.1-1.6,3.3-2.1,5-1c1,0.7,2.1,1.4,3.3,2.1c8.2,5,15.9,7.5,22.2,7.5
	c5.7,0,10.4-2.2,13.3-6.5v0c0.2-0.3,0.4-0.5,0.4-0.7c0.8-1.4,1.5-2.8,1.9-4.2c0.8-2.9,0.7-5.9-0.5-8.6c-1.2-2.9-3.7-5.6-7.5-7.9l0,0
	c-2.5-1.5-5.7-2.9-9.5-4c-9.7-2.7-16.8-6.6-20.7-11.7c-4.2-5.7-4.5-12.5,0-20.8c3-5.5,9.1-8.9,16.9-9.5c6.7-0.5,14.8,1.1,23,5.3
	c1.8,0.9,2.5,3.1,1.6,4.8c-0.9,1.8-3.1,2.5-4.8,1.6c-7-3.6-13.7-4.9-19.2-4.5c-5.3,0.4-9.4,2.5-11.1,5.8c-3,5.5-3,9.8-0.5,13.1
	c2.8,3.8,8.7,6.8,16.8,9.1c4.4,1.2,8.1,2.9,11.2,4.8h0c5.1,3.1,8.5,7,10.3,11.2c1.9,4.3,2.1,9,0.8,13.4c-0.6,2-1.5,4.1-2.7,6
	c-0.3,0.4-0.5,0.8-0.6,1l0,0c-4.3,6.4-11.1,9.6-19.3,9.6c-7.6,0-16.5-2.8-25.9-8.5C13.7,483.7,12.5,483,11.2,482.1z"
                        />
                        <path
                          class="st0"
                          d="M75.1,415.2c0-1.9,1.6-3.4,3.6-3.4c2,0,3.6,1.5,3.6,3.4v74.3c0,1.9-1.6,3.4-3.6,3.4c-2,0-3.6-1.5-3.6-3.4V415.2
	z"
                        />
                        <path
                          class="st0"
                          d="M82.5,461.5c-1.3,1.5-3.5,1.8-5.1,0.5c-1.5-1.3-1.8-3.5-0.5-5.1c5.5-6.7,11.7-10.8,17.8-12.6
	c4.4-1.3,8.7-1.4,12.4-0.4c3.9,1.1,7.3,3.3,9.7,6.7c2.3,3.2,3.6,7.3,3.6,12.2v26.6c0,2-1.6,3.6-3.6,3.6c-2,0-3.6-1.6-3.6-3.6v-26.6
	c0-3.4-0.8-6.1-2.2-8.1c-1.4-1.9-3.3-3.2-5.7-3.9c-2.5-0.7-5.4-0.6-8.5,0.3C92,452.6,87,456,82.5,461.5z"
                        />
                        <path
                          class="st0"
                          d="M155.7,443.2c6.9,0,13.1,2.8,17.6,7.3l0,0c4.5,4.5,7.3,10.7,7.3,17.6c0,6.9-2.8,13.1-7.3,17.6
	c-4.5,4.5-10.7,7.3-17.6,7.3s-13.1-2.8-17.6-7.3v0c-4.5-4.5-7.3-10.7-7.3-17.6c0-6.9,2.8-13.1,7.3-17.6v0
	C142.6,446,148.9,443.2,155.7,443.2L155.7,443.2z M168.3,455.5c-3.2-3.2-7.7-5.2-12.6-5.2c-4.9,0-9.3,2-12.6,5.2l0,0
	c-3.2,3.2-5.2,7.7-5.2,12.6c0,4.9,2,9.4,5.2,12.6l0,0c3.2,3.2,7.7,5.2,12.6,5.2s9.3-2,12.6-5.2c3.2-3.2,5.2-7.7,5.2-12.6
	C173.5,463.2,171.5,458.7,168.3,455.5z"
                        />
                        <path
                          class="st0"
                          d="M190.8,446.8c0-2,1.6-3.6,3.6-3.6c2,0,3.6,1.6,3.6,3.6v4.5c0.3-0.3,0.5-0.6,0.8-0.8v0
	c4.5-4.5,10.7-7.3,17.6-7.3c6.9,0,13.1,2.8,17.6,7.3l0,0c4.5,4.5,7.3,10.7,7.3,17.6c0,6.9-2.8,13.1-7.3,17.6
	c-4.5,4.5-10.7,7.3-17.6,7.3s-13.1-2.8-17.6-7.3v0c-0.3-0.3-0.5-0.6-0.8-0.8v32.7c0,2-1.6,3.6-3.6,3.6c-2,0-3.6-1.6-3.6-3.6V446.8
	L190.8,446.8z M228.9,455.5c-3.2-3.2-7.7-5.2-12.6-5.2c-4.9,0-9.3,2-12.6,5.2l0,0c-3.2,3.2-5.2,7.7-5.2,12.6c0,4.9,2,9.4,5.2,12.6
	l0,0c3.2,3.2,7.7,5.2,12.6,5.2c4.9,0,9.3-2,12.6-5.2c3.2-3.2,5.2-7.7,5.2-12.6C234.1,463.2,232.1,458.7,228.9,455.5z"
                        />
                        <path
                          class="st0"
                          d="M275.1,443.2c6.1,0,11.7,2.4,15.8,6.6c3.4,3.4,4.8,6.8,6,10.4c3.1,10.1-2.8,9.7-4.6,9.7h-33.1
	c0.3,2.4,0.9,4.6,1.6,6.6c1.3,3.1,3.2,5.4,5.6,7c2.5,1.6,5.7,2.4,9.4,2.4c4,0,8.7-1.1,13.9-3.3c1.8-0.8,3.9,0.1,4.7,1.9
	s-0.1,3.9-1.9,4.7c-6,2.5-11.6,3.7-16.6,3.8c-5.1,0.1-9.6-1.1-13.3-3.5c-3.7-2.4-6.9-5.7-8.4-10.3c-3.4-10.1-3.5-20.1,5.1-29.4
	C263.3,445.5,269,443.2,275.1,443.2L275.1,443.2z M285.9,454.9c-2.9-2.8-6.7-4.5-10.8-4.5c-4.1,0-7.9,1.7-10.8,4.5
	c-2.1,2.1-3.8,4.7-4.7,7.7h31.1C289.6,459.6,288,457,285.9,454.9z"
                        />
                        <path
                          class="st0"
                          d="M329.7,443.2c6.1,0,11.7,2.4,15.8,6.6c3.4,3.4,4.8,6.8,6,10.4c3.1,10.1-2.8,9.7-4.6,9.7h-33.1
	c0.3,2.4,0.9,4.6,1.6,6.6c1.3,3.1,3.2,5.4,5.6,7c2.5,1.6,5.7,2.4,9.4,2.4c4,0,8.7-1.1,13.9-3.3c1.8-0.8,3.9,0.1,4.7,1.9
	c0.8,1.8-0.1,3.9-1.9,4.7c-6,2.5-11.6,3.7-16.6,3.8c-5.1,0.1-9.6-1.1-13.3-3.5c-3.7-2.4-6.9-5.7-8.4-10.3
	c-3.4-10.1-3.5-20.1,5.1-29.4C317.9,445.5,323.6,443.2,329.7,443.2L329.7,443.2z M340.5,454.9c-2.9-2.8-6.7-4.5-10.8-4.5
	c-4.1,0-7.9,1.7-10.8,4.5c-2.1,2.1-3.8,4.7-4.7,7.7h31.1C344.2,459.6,342.6,457,340.5,454.9z"
                        />
                        <path
                          class="st0"
                          d="M36.5,118.1h75.3c2.3-25.5,9.4-48.4,19.6-66.1c13.5-23.4,32.7-37.8,54.4-37.8c21.7,0,40.8,14.5,54.4,37.8
	c10.2,17.6,17.3,40.6,19.6,66.1H335c8.9,0,16.9,7.3,16.2,16.2L336.1,345c-1.9,26.4-18.5,41.5-42.6,41.5H78.1
	c-26.8,0-41-18.7-42.6-41.5L20.3,134.4C19.7,125.5,27.6,118.1,36.5,118.1L36.5,118.1z M130.6,118.1h110.2
	c-2.2-22.1-8.3-41.7-17-56.7C213.8,43.9,200.2,33,185.8,33c-14.5,0-28,10.9-38.2,28.5C138.9,76.4,132.9,96.1,130.6,118.1z"
                        />
                        <path
                          class="st1"
                          d="M131.1,312.6c-3.5-2.4-4.4-7.1-2-10.6c2.4-3.5,7.1-4.4,10.6-2c2.2,1.5,4.5,3,7,4.5c17.6,10.7,33.8,16,47.3,16
	c12.2,0,22.1-4.6,28.4-13.9v0c0.4-0.7,0.8-1.1,0.9-1.4c1.8-2.9,3.1-6,4-9c1.8-6.2,1.5-12.6-1-18.4c-2.7-6.1-7.9-12-16-16.9l0,0
	c-5.4-3.3-12.1-6.2-20.2-8.4c-20.6-5.8-35.8-14-44-25c-9-12.1-9.7-26.7-0.1-44.4c6.4-11.7,19.5-19,36.1-20.3
	c14.3-1.1,31.5,2.3,49,11.2c3.8,1.9,5.3,6.5,3.3,10.3s-6.5,5.3-10.3,3.3c-14.9-7.6-29.1-10.5-40.9-9.6c-11.3,0.9-19.9,5.3-23.7,12.3
	c-6.4,11.7-6.3,20.9-1.1,27.9c6,8.1,18.5,14.4,35.9,19.3c9.4,2.7,17.4,6.1,24,10.1h0c10.9,6.6,18.2,14.9,22.1,23.9
	c4,9.3,4.5,19.1,1.8,28.7c-1.2,4.3-3.1,8.6-5.7,12.8c-0.6,0.9-1,1.6-1.3,2.1l0,0c-9.3,13.7-23.6,20.5-41,20.6
	c-16.3,0-35.3-6-55.3-18.2C136.4,316.1,133.8,314.5,131.1,312.6z"
                        />
                      </svg>
                    </a>
                  </div>

                  <div
                    v-if="agent.status === 'SEDANG DIVERIFIKASI'"
                    class="flex"
                  >
                    <button
                      @click="verifyAgent(agent.id, true)"
                      class="inline-flex items-center px-2 py-1 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 transition ease-in-out duration-150"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        width="20"
                        height="20"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M5 13l4 4L19 7"
                        />
                      </svg>
                    </button>
                    <button
                      @click="verifyAgent(agent.id, false)"
                      class="inline-flex items-center px-2 py-1 ml-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 transition ease-in-out duration-150"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        width="20"
                        height="20"
                        stroke="currentColor"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"
                        />
                      </svg>
                    </button>
                  </div>

                  <div v-if="agent.status === 'AKTIF'">
                    <span
                      class="flex rounded-full py-1 px-2 bg-green-500 text-white"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        height="24"
                        width="24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                      </svg>
                      <span class="mx-1">{{ agent.total_point || 0 }}</span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="agents.length === 0" class="text-center px-3">
        <div class="bg-white px-4 py-2 shadow">
          Tidak ada data untuk ditampilkan.
        </div>
      </div>
    </div>

  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import JetButton from "./../../JetStream/Button";
import UserStatus from "./../../JetStream/UserStatus";
import Label from "./../../JetStream/Label";

export default {
  components: {
    AppLayout,
    UserStatus,
    JetButton,
    Label,
  },

  data() {
    return {
      agents: [],
      filter: "Semua",
      is_fetching: false,
      search: "",
    };
  },

  mounted() {
    this.fetchAgents();
  },

  methods: {
    changeSearch($event) {
      this.search = $event.target.value;
      this.status = "Semua";
      this.fetchAgents();
    },
    changeFilter($event) {
      this.filter = $event.target.value;
      this.fetchAgents();
    },
    async fetchAgents() {
      this.is_fetching = true;
      try {
        const {
          data: { data },
        } = await axios.get("/api/admin/user", {
          params: {
            type: 'AGENT',
            status: this.filter === "Semua" ? undefined : this.filter.toUpperCase(),
            search: this.search,
          },
        });
        this.agents = data;
        this.is_fetching = false;
      } catch (e) {
        console.log(e);
        this.is_fetching = false;
      }
    },
    async verifyAgent(id, is_verified) {
      try {
        const {
          data: { data },
        } = await axios.post(`/api/admin/agent/${id}/verify`, {
          is_verified,
        });
        const agent = this.agents.find((agent) => agent.id === id);
        agent.status = data.status;
      } catch (e) {
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
.st0 {
  fill: #ea501f;
}
.st1 {
  fill: #ffffff;
}
</style>
<template>
  <div v-if="isShow" class="text-center px-3">
    <div class="bg-white px-4 py-2 shadow">
      Tidak ada data untuk ditampilkan.
    </div>
  </div>
</template>

<script>
export default {
    props: ['isShow']
}
</script>
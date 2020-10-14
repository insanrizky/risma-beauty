<template>
  <span
    class="px-2 py-1 text-white rounded font-bold text-sm"
    :class="statusChipColor"
    >{{ status }}</span
  >
</template>

<script>
export default {
  props: ["status"],

  computed: {
    statusChipColor() {
      let color = "bg-gray-800";
      const { status } = this;
      switch (status) {
        case "DALAM PROSES PENDAFTARAN":
          color = "bg-gray-800";
          break;
        case "SEDANG DIVERIFIKASI":
          color = "bg-orange-400";
          break;
        case "AKTIF":
          color = "bg-green-400";
          break;
        case "GAGAL VERIFIKASI":
          color = "bg-red-600";
          break;
        default:
      }

      return color;
    },
  },
};
</script>
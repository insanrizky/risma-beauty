<template>
  <span class="px-2 py-1 text-white rounded font-bold text-sm" :class="bgColor">
    <slot></slot>
  </span>
</template>

<script>
export default {
  props: ["bgColor"],
};
</script>

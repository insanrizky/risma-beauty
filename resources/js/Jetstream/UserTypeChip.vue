<template>
    <span class="ml-1 px-2 py-1 text-white text-xs font-bold rounded" :class="chipColor">{{ type }}</span>
</template>

<script>
export default {
    props: ['type'],
    computed: {
        chipColor() {
            switch(this.type) {
                case 'AGENT':
                    return 'bg-purple-500';
                case 'RESELLER':
                    return 'bg-blue-500';
                case 'ADMIN':
                    return 'bg-green-500';
                default:
                    return '';
            }
        }
    }
}
</script>
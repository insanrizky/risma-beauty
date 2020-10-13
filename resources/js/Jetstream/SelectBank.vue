<template>
    <select @change="$emit('input', $event.target.value)" class="form-input w-full mt-1 rounded">
        <option value="">-- Pilih Bank --</option>
        <option :selected="opt.id === value" v-for="opt in options" v-bind:value="opt.id" :key="opt.id">{{ opt.name }}</option>
    </select>
</template>

<script>
    export default {
        props: ['value'],
        data() {
            return {
                options: [],
            };
        },
        mounted(){
            this.getBanks();
        },
        methods: {
            async getBanks() {
                try {
                    const { data } = await axios.get(`/api/bank`);
                    this.options = data;
                } catch (e) {
                    console.log(e);
                }
            },
        }
    }
</script>


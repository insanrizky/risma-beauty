<template>
    <select @change="$emit('input', $event.target.value)" class="form-input w-full mt-1 rounded">
        <option selected disabled value="">-- Pilih Provinsi --</option>
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
        mounted() {
            this.getProvinces();
        },
        methods: {
            async getProvinces() {
                try {
                    const { data } = await axios.get('/api/province');
                    this.options = data;
                } catch (e) {
                    console.log(e);
                }
            },
        }
    }
</script>


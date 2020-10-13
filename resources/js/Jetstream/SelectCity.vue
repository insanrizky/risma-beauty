<template>
    <select @change="$emit('input', $event.target.value)" class="form-input w-full mt-1 rounded">
        <option selected disabled value="">-- Pilih Kabupaten/Kota --</option>
        <option :selected="opt.id === value" v-for="opt in options" v-bind:value="opt.id" :key="opt.id">{{ opt.name }}</option>
    </select>
</template>

<script>
    export default {
        props: ['province_id', 'value'],
        data() {
            return {
                options: [],
            };
        },
        watch: {
            province_id: function(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.getCities();
                }
            },
        },
        mounted() {
            if (this.province_id) {
                this.getCities();
            }
        },
        methods: {
            async getCities() {
                try {
                    const { data } = await axios.get(`/api/city/${this.province_id}`);
                    this.options = data;
                } catch (e) {
                    console.log(e);
                }
            },
        }
    }
</script>


<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ubah Kata Sandi
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-password-form class="mt-10 sm:mt-0" />
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import UpdatePasswordForm from './UpdatePasswordForm'

    export default {
        props: ['sessions'],

        components: {
            AppLayout,
            UpdatePasswordForm,
        },
    }
</script>

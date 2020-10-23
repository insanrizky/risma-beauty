<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <!-- <x-jet-authentication-card-logo /> -->
            <img src="{{ asset('/img/risma_logo.png') }}" width="400" />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Lupa kata sandimu? Gak usah khawatir! Masukin aja email kamu di sini, nanti kami kirim tautan ke email kamu supaya kamu bisa mengatur ulang kata sandi kamu.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div class="flex items-center md:flex-row flex-col justify-end mt-4">
                <x-jet-button>
                    {{ __('Atur ulang kata sandi saya') }}
                </x-jet-button>
                <a href="/" class="ml-0 md:ml-2 mt-2 md:mt-0">
                    <x-jet-secondary-button>
                        {{ __('Kembali') }}
                    </x-jet-secondary-button>
                </a>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>

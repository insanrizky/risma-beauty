<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <!-- <x-jet-authentication-card-logo /> -->
            <img src="{{ asset('/img/risma_logo.png') }}" width="400" />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <h1 class="font-bold">FORM REGISTRASI AGEN dan RESELLER</h1>
            
            <hr class="my-4"/>

            <div>
                <x-jet-label for="type" value="{{ __('Sebagai') }}" />
                <select id="type" onchange="typeChange()" name="type" autofocus required class="form-input w-full mt-1 rounded">
                    <option value="" disabled selected>-- Pilih Sebagai --</option>
                    <option value="AGENT">Agen</option>
                    <option value="RESELLER">Reseller</option>
                </select>
            </div>

            <div class="mt-4" id="agent_id_section">
                <x-jet-label for="agent_id" value="{{ __('ID Agen') }}" />
                <x-jet-input id="agent_id" class="block mt-1 w-full" type="text" name="upline_identifier" :value="old('upline_identifier')" />
            </div>

            <div class="mt-4">
                <x-jet-label for="name" value="{{ __('Nama Lengkap') }}" />
                <x-jet-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Kata Sandi') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password_confirmation" value="{{ __('Konfirmasi Kata Sandi') }}" />
                <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Sudah terdaftar?') }}
                </a>

                <x-jet-button class="ml-4">
                    {{ __('Daftar') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>

<script>
    const agentIdDOM = document.getElementById('agent_id_section');
    const agentIdInput = document.getElementById('agent_id');
    agentIdDOM.style.display = 'none';
    agentIdInput.removeAttribute("required");

    function typeChange() {
        const typeDOM = document.getElementById('type');
        if (typeDOM.value === 'RESELLER') {
            agentIdDOM.style.display = 'block';
            agentIdInput.setAttribute("required", true);
        } else {
            agentIdDOM.style.display = 'none';
            agentIdInput.removeAttribute("required");
        }
    }
</script>
<template>
  <jet-form-section @submitted="updatePointSetting">
    <template #title> Konversi Poin </template>

    <template #description>
      Atur konversi poin untuk Agen dan Reseller.
    </template>

    <template #form>
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="amount-agent" value="Nominal Agen per Poin" />
        <jet-input
          id="amount-agent"
          type="text"
          class="mt-1 block w-full"
          required
          placeholder="Hanya isi dengan angka"
          v-model="form.amount_agent"
          autocomplete="off"
        />
        <jet-input-error :message="form.error('amount_agent')" class="mt-2" />
      </div>
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="amount-reseller" value="Nominal Reseller per Poin" />
        <jet-input
          id="amount-reseller"
          type="text"
          class="mt-1 block w-full"
          required
          placeholder="Hanya isi dengan angka"
          v-model="form.amount_reseller"
          autocomplete="off"
        />
        <jet-input-error
          :message="form.error('amount_reseller')"
          class="mt-2"
        />
      </div>
    </template>
    <template #actions>
      <jet-action-message :on="form.recentlySuccessful" class="mr-3">
        Tersimpan.
      </jet-action-message>

      <jet-button
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        Simpan
      </jet-button>
    </template>
  </jet-form-section>
</template>

<script>
import JetButton from "./../../Jetstream/Button";
import JetFormSection from "./../../Jetstream/FormSection";
import JetInput from "./../../Jetstream/Input";
import JetInputError from "./../../Jetstream/InputError";
import JetLabel from "./../../Jetstream/Label";
import JetActionMessage from "./../../Jetstream/ActionMessage";
import JetSecondaryButton from "./../../Jetstream/SecondaryButton";
export default {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
  },
  data() {
    return {
      form: this.$inertia.form(
        {
          _method: "PUT",
          amount_agent: this.$page.amount_agent,
          amount_reseller: this.$page.amount_reseller,
        },
        {
          bag: "updatePointSetting",
          resetOnSuccess: false,
        }
      ),
    };
  },
  methods: {
    updatePointSetting() {
      this.form
        .post(route("admin.update-point-setting"), {
          preserveScroll: true,
        })
        .then(() => {
          if (this.$page.errors.message) {
            this.$swal("Terjadi Kesalahan!", "", "error");
            return;
          }
          this.$swal("Berhasil!", "Konversi poin berhasil diperbarui", "success");
        });
    },
  },
};
</script>
<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Tambah Klaim Poin
      </h2>
    </template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <jet-form-section>
        <template #title> Klaim Penjualan </template>

        <template #description>
          Ayo klaim hasil penjualan kamu di sini.
        </template>
        <template #form>
          <div class="col-span-6 sm:col-span-4">
            <jet-label for="total-pcs" value="Total Item (Pcs)" />
            <jet-input
              id="total-pcs"
              type="text"
              class="mt-1 block w-full"
              required
              placeholder="Hanya isi dengan angka"
              v-model="form.total_item"
              autocomplete="off"
            />
            <jet-input-error :message="error.total_item" class="mt-2" />
          </div>

          <div class="col-span-6 sm:col-span-4">
            <input
              type="file"
              class="hidden"
              ref="payment_file"
              @change="updateFile"
              required
            />

            <jet-label
              for="payment_file"
              value="Bukti Pembayaran (Maksimum: 2 MB)"
            />

            <div class="mt-2" v-show="filePreview">
              <span
                class="block w-40 h-40"
                :style="
                  'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
                  filePreview +
                  '\');'
                "
              >
              </span>
            </div>

            <jet-secondary-button
              class="mt-2 mr-2"
              type="button"
              @click.native.prevent="selectNewPhoto"
            >
              Unggah Bukti Pembayaran
            </jet-secondary-button>
            <jet-input-error :message="error.payment_file" class="mt-2" />
          </div>
        </template>

        <template #actions>
          <jet-button
            :class="{ 'opacity-25': is_processing }"
            :disabled="is_processing"
            @click.native.prevent="claimPoints"
            type="submit"
          >
            Klaim
          </jet-button>
          <inertia-link :href="route('admin.show-points')" ref="back">
            <jet-secondary-button class="ml-2"> Kembali </jet-secondary-button>
          </inertia-link>
        </template>
      </jet-form-section>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "./../../Layouts/AppLayout";
import JetButton from "./../../Jetstream/Button";
import JetFormSection from "./../../Jetstream/FormSection";
import JetInput from "./../../Jetstream/Input";
import JetInputError from "./../../Jetstream/InputError";
import JetLabel from "./../../Jetstream/Label";
import JetActionMessage from "./../../Jetstream/ActionMessage";
import JetSecondaryButton from "./../../Jetstream/SecondaryButton";

export default {
  components: {
    AppLayout,
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
      filePreview: null,
      is_processing: false,
      form: {
        id: this.$page.user.id,
        total_item: "",
        payment_file: null,
      },
      error: {
        total_item: null,
        payment_file: null,
      },
    };
  },

  methods: {
    updateFile() {
      this.resetValidation();
      const reader = new FileReader();

      reader.onload = (e) => {
        this.filePreview = e.target.result;
      };

      reader.readAsDataURL(this.$refs.payment_file.files[0]);
    },
    selectNewPhoto() {
      this.$refs.payment_file.click();
    },
    resetValidation() {
      this.error = { total_item: "", payment_file: "" };
    },
    validateForm() {
      this.resetValidation();
      if (!this.form.total_item) {
        this.error.total_item = "Wajib diisi";
        return false;
      }

      if (this.$refs.payment_file.files.length === 0) {
        this.error.payment_file = "Wajib diisi";
        return false;
      }

      const { size, type } = this.$refs.payment_file.files[0];
      if (size > 2 * 1000 * 1000) {
        this.error.payment_file = "Ukuran berkas melebihi 2MB!";
        return false;
      }

      if (
        type !== "image/png" &&
        type !== "image/jpeg" &&
        type !== "image/jpg"
      ) {
        this.error.payment_file = "Berkas harus berupa JPG/PNG";
        return false;
      }

      return true;
    },
    async claimPoints() {
      if (!this.validateForm()) {
        return false;
      }

      try {
        this.is_processing = true;

        if (this.$refs.payment_file) {
          this.form.payment_file = this.$refs.payment_file.files[0];
        }

        const payload = new FormData();
        payload.append("id", this.form.id);
        payload.append("payment_file", this.form.payment_file);
        payload.append("total_item", this.form.total_item);

        const {
          data: { data },
        } = await axios.post("/api/point/claim", payload);

        this.is_processing = false;
        this.$swal("Berhasil!", "Klaim berhasil dibuat", "success");

        this.$refs.back.click();
      } catch (e) {
        this.is_processing = false;
        console.log(e);
        this.$swal("Terjadi Kesalahan!", "", "error");
      }
    },
  },
};
</script>
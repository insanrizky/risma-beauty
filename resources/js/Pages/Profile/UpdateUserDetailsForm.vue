<template>
  <jet-form-section @submitted="updateUserDetail">
    <template #title> Informasi Profil Anda </template>

    <template #description> Perbarui informasi profil Anda di sini. </template>

    <template #form>
      <!-- Contact -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="contact" value="No. HP (Whatsapp Aktif)" />
        <jet-input
          id="contact"
          type="text"
          required
          placeholder="Nomor Handphone"
          class="mt-1 block w-full"
          v-model="form.contact"
          autocomplete="contact"
        />
        <jet-input-error :message="form.error('contact')" class="mt-2" />
      </div>

      <!-- Province -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="province" value="Provinsi" />
        <jet-select-province
          id="province"
          required
          v-model="form.province_id"
        />
        <jet-input-error :message="form.error('province_id')" class="mt-2" />
      </div>

      <!-- City -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="city" value="Kabupaten/Kota" />
        <jet-select-city
          id="city"
          required
          v-model="form.city_id"
          :province_id="form.province_id"
        />
        <jet-input-error :message="form.error('city_id')" class="mt-2" />
      </div>

      <!-- Address -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="address" value="Alamat" />
        <jet-input
          id="address"
          type="text"
          required
          placeholder="Alamat"
          class="mt-1 block w-full"
          v-model="form.address"
          autocomplete="address"
        />
        <jet-input-error :message="form.error('address')" class="mt-2" />
      </div>

      <!-- Bank -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="bank_id" value="Bank" />
        <jet-select-bank id="bank_id" required v-model="form.bank_id" />
        <div class="mt-2 bg-orange-100 text-orange-700 px-4 py-2" role="alert">
          <p>
            Diwajibkan memiliki rekening
            <span class="font-bold">Bank BRI</span>.
          </p>
        </div>
        <jet-input-error :message="form.error('bank_id')" class="mt-2" />
      </div>

      <!-- Account Number -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="account_number" value="Nomor Rekening" />
        <jet-input
          id="account_number"
          type="text"
          required
          placeholder="Nomor Rekening"
          class="mt-1 block w-full"
          v-model="form.account_number"
          autocomplete="account_number"
        />
        <jet-input-error :message="form.error('account_number')" class="mt-2" />
      </div>

      <!-- Instagram Link -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="instagram_link" value="Instagram Link" />
        <jet-input
          id="instagram_link"
          type="text"
          required
          placeholder="https://instagram.com/username"
          class="mt-1 block w-full"
          v-model="form.instagram_link"
          autocomplete="instagram_link"
        />
        <jet-input-error :message="form.error('instagram_link')" class="mt-2" />
      </div>

      <!-- Shopee Link -->
      <div class="col-span-6 sm:col-span-4">
        <jet-label for="shopee_link" value="Shopee Link (Opsional)" />
        <jet-input
          id="shopee_link"
          type="text"
          placeholder="https://shopee.co.id/username"
          class="mt-1 block w-full"
          v-model="form.shopee_link"
          autocomplete="shopee_link"
        />
        <jet-input-error :message="form.error('shopee_link')" class="mt-2" />
      </div>

      <!-- KTP -->
      <div class="col-span-6 sm:col-span-4">
        <input
          type="file"
          class="hidden"
          ref="ktp_file"
          @change="updateKtpPreview"
        />

        <jet-label for="ktp_file" value="KTP (Maksimum: 2 MB)" />

        <div
          class="mt-2"
          v-show="!ktpPreview && $page.user_detail.ktp_file_url"
        >
          <img
            :src="$page.user_detail.ktp_file_url"
            alt="KTP"
            class="w-40 h-40 object-cover"
          />
        </div>

        <div class="mt-2" v-show="ktpPreview">
          <span
            class="block w-40 h-40"
            :style="
              'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
              ktpPreview +
              '\');'
            "
          >
          </span>
        </div>

        <jet-secondary-button
          v-if="!$page.user_detail.ktp_file || isEditable"
          class="mt-2 mr-2"
          type="button"
          @click.native.prevent="selectNewKtp"
        >
          Unggah KTP
        </jet-secondary-button>

        <jet-input-error :message="errors.ktp_file" class="mt-2" />
      </div>

      <!-- Bukti Pembayaran -->
      <div v-if="$page.user.type === 'AGENT'" class="col-span-6 sm:col-span-4">
        <input
          type="file"
          class="hidden"
          ref="payment_file"
          @change="updatePaymentPreview"
        />

        <jet-label for="payment_file" value="Bukti Pembayaran (Maksimum: 2 MB)" />

        <div
          class="mt-2"
          v-show="!paymentPreview && $page.user_detail.payment_file_url"
        >
          <img
            :src="$page.user_detail.payment_file_url"
            alt="Bukti Pembayaran"
            class="w-40 h-40 object-cover"
          />
        </div>

        <div class="mt-2" v-show="paymentPreview">
          <span
            class="block w-40 h-40"
            :style="
              'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' +
              paymentPreview +
              '\');'
            "
          >
          </span>
        </div>

        <jet-secondary-button
          v-if="isEditable"
          class="mt-2 mr-2"
          type="button"
          @click.native.prevent="selectNewPayment"
        >
          Unggah Bukti Pembayaran
        </jet-secondary-button>

        <jet-input-error :message="errors.payment_file" class="mt-2" />
      </div>
    </template>

    <template #actions>
      <jet-button
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        Simpan
      </jet-button>

      <inertia-link :href="route('dashboard')" ref="back" class="ml-3">
        <jet-secondary-button>Kembali</jet-secondary-button>
      </inertia-link>
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
import JetSelectProvince from "./../../Jetstream/SelectProvince";
import JetSelectCity from "./../../Jetstream/SelectCity";
import JetSelectBank from "./../../Jetstream/SelectBank";

export default {
  components: {
    JetActionMessage,
    JetButton,
    JetFormSection,
    JetInput,
    JetInputError,
    JetLabel,
    JetSecondaryButton,
    JetSelectProvince,
    JetSelectCity,
    JetSelectBank,
  },

  data() {
    return {
      is_processed: false,
      form: this.$inertia.form(
        {
          _method: "PUT",
          contact: this.$page.user_detail.contact,
          province_id: this.$page.user_detail.province_id,
          city_id: this.$page.user_detail.city_id,
          address: this.$page.user_detail.address,
          bank_id: this.$page.user_detail.bank_id,
          account_number: this.$page.user_detail.account_number,
          ktp_file: this.$page.user_detail.ktp_file,
          instagram_link: this.$page.user_detail.instagram_link,
          shopee_link: this.$page.user_detail.shopee_link,
          payment_file: this.$page.user_detail.payment_file,
        },
        {
          bag: "updateUserDetail",
          resetOnSuccess: false,
        }
      ),
      errors: {
        ktp_file: null,
        payment_file: null,
      },

      ktpPreview: null,
      paymentPreview: null,
    };
  },

  computed: {
    isEditable() {
      const {
        user_detail: { status },
      } = this.$page;
      return (
        status === "DALAM PROSES PENDAFTARAN" ||
        status === "SEDANG DIVERIFIKASI" ||
        status === "GAGAL VERIFIKASI"
      );
    },
  },

  methods: {
    resetValidation() {
      this.errors = {
        ktp_file: null,
        payment_file: null,
      };
    },
    validateFile(attribute) {
      if (this.$refs[attribute].files.length === 0) {
        this.errors[attribute] = "Wajib diisi";
        return false;
      }

      const { size, type } = this.$refs[attribute].files[0];
      if (size > 2 * 1000 * 1000) {
        this.errors[attribute] = "Ukuran berkas melebihi 2MB!";
        return false;
      }

      if (
        type !== "image/png" &&
        type !== "image/jpeg" &&
        type !== "image/jpg"
      ) {
        this.errors[attribute] = "Berkas harus berupa JPG/PNG";
        return false;
      }

      return true;
    },
    validateForm() {
      this.resetValidation();
      if (this.$page.user.type === "AGENT") {
        const isValidPaymentFile = this.validateFile("payment_file");
        if (!isValidPaymentFile) {
          return false;
        }
      }

      const isValidKtpFile = this.validateFile("ktp_file");
      if (!isValidKtpFile) {
        return false;
      }

      return true;
    },
    updateUserDetail() {
      if (!this.validateForm()) {
        return false;
      }

      if (this.$refs.ktp_file) {
        this.form.ktp_file = this.$refs.ktp_file.files[0];
      }

      if (this.$refs.payment_file) {
        this.form.payment_file = this.$refs.payment_file.files[0];
      }

      this.form
        .post(route("user-detail.update"), {
          preserveScroll: true,
        })
        .then(() => {
          this.is_processed = true;
          if (this.$page.errors.failed) {
            this.$swal("Terjadi Kesalahan!", "", "error");
            return;
          }

          this.ktpPreview = null;
          this.paymentPreview = null;
          this.$swal("Berhasil!", "Profil sudah disimpan", "success");
          this.$refs.back.click();
        });
    },

    selectNewKtp() {
      this.$refs.ktp_file.click();
    },

    selectNewPayment() {
      this.$refs.payment_file.click();
    },

    updateKtpPreview() {
      this.resetValidation();
      const reader = new FileReader();

      reader.onload = (e) => {
        this.ktpPreview = e.target.result;
      };

      reader.readAsDataURL(this.$refs.ktp_file.files[0]);
    },

    updatePaymentPreview() {
      this.resetValidation();
      const reader = new FileReader();

      reader.onload = (e) => {
        this.paymentPreview = e.target.result;
      };

      reader.readAsDataURL(this.$refs.payment_file.files[0]);
    },
  },
};
</script>

<template>
  <form @submit.prevent="submitForm">
    <div class="mb-4">
      <label for="nom" class="block text-gray-700">Nom</label>
      <input
          v-model="formData.nom"
          type="text"
          id="nom"
          class="border rounded w-full py-2 px-3"
          placeholder="Nom du produit"
      />
    </div>
    <div class="mb-4">
      <label for="description" class="block text-gray-700">Description</label>
      <textarea
          v-model="formData.description"
          id="description"
          class="border rounded w-full py-2 px-3"
          placeholder="Description du produit"
      ></textarea>
    </div>
    <div class="mb-4">
      <label for="quantité" class="block text-gray-700">Quantité</label>
      <input
          v-model="formData.quantité"
          type="number"
          id="quantité"
          class="border rounded w-full py-2 px-3"
          placeholder="Quantité"
      />
    </div>
    <div class="mb-4">
      <label for="image" class="block text-gray-700">Image du produit</label>
      <input
          type="file"
          id="image"
          @change="handleImageChange"
          class="border rounded w-full py-2 px-3"
          accept="image/*"
      />
    </div>
    <div class="mb-4">
      <label for="file_product" class="block text-gray-700">Fichier technique (PDF)</label>
      <input
          type="file"
          id="file_product"
          @change="handleFileChange"
          class="border rounded w-full py-2 px-3"
          accept=".pdf"
      />
    </div>
    <button
        type="submit"
        class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
    >
      Enregistrer
    </button>
  </form>
</template>

<script>
export default {
  name: "FormulaireProduit",
  props: {
    produit: {
      type: Object,
      default: () => ({}),
    },
    isEditMode: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      formData: {
        nom: "",
        description: "",
        quantité: null,
        image: null,
        file_product: null,
      },
    };
  },
  watch: {
    produit: {
      immediate: true,
      handler(newProduit) {
        if (newProduit) {
          this.formData = { ...newProduit };
        }
      },
    },
  },
  methods: {
    submitForm() {
      const formData = new FormData();
      formData.append("nom", this.formData.nom);
      formData.append("description", this.formData.description);
      formData.append("quantité", this.formData.quantité);

      if (this.formData.image) {
        formData.append("image", this.formData.image);
      }

      if (this.formData.file_product) {
        formData.append("file_product", this.formData.file_product);
      }

      console.log(this.formData);
      this.$emit("submit", formData);
    },

    handleImageChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.image = file;
      }
    },

    handleFileChange(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.file_product = file;
      }
    },

    // Méthode pour réinitialiser formData après mise à jour
    resetFormData(newProduit) {
      this.formData = { ...newProduit };
    },
  },

};
</script>

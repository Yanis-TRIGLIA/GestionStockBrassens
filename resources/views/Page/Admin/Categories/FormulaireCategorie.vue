<template>
    <form @submit.prevent="submitForm" class="max-w-lg">
        <div class="mb-4">
            <label for="nom" class="block text-gray-700">Nom de la Catégorie</label>
            <input
                type="text"
                id="nom"
                v-model="form.nom"
                class="mt-1 block w-full border rounded py-2 px-3"
                placeholder="Nom de la catégorie"
                required
            />
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700">Image de la Catégorie</label>
            <input
                type="file"
                id="image"
                @change="handleFileUpload"
                class="border rounded w-full py-2 px-3"
            />
            <p v-if="form.image" class="text-sm text-gray-600 mt-2">Image actuelle : {{ form.image.name }}</p>
        </div>

        <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
            {{ categorie ? "Mettre à jour" : "Créer" }}
        </button>
    </form>
</template>

<script>
export default {
    name: "FormulaireCategorie",
    props: {
        categorie: {
            type: Object,
            default: () => null,
        },
    },
    data() {
        return {
            form: {
                nom: this.categorie ? this.categorie.nom : "",
                image: null,
            },
        };
    },
    watch: {
        categorie: {
            handler(newValue) {
                this.form.nom = newValue ? newValue.nom : "";
                this.form.image = null; // Reset image upload
            },
            immediate: true,
        },
    },
    methods: {
        handleFileUpload(event) {
            const file = event.target.files[0];
            this.form.image = file;
        },
        submitForm() {
            const formData = new FormData();
            formData.append("nom", this.form.nom);
            if (this.form.image) {
                formData.append("image", this.form.image);
            }
            this.$emit("submit", formData);
        },
    },
};
</script>

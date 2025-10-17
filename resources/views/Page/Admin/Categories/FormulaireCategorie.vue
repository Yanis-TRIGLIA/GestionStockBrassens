<template>
    <form @submit.prevent="submitForm" class="max-w-lg mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">{{ categorie ? 'Modifier la catégorie' : 'Créer une catégorie' }}
            </h2>

            <div class="mb-4">
                <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom de la catégorie</label>
                <input type="text" id="nom" v-model="form.nom"
                    class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                    placeholder="Nom de la catégorie" required />
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image de la catégorie</label>
                <div class="border-dashed border-2 border-indigo-200 rounded-md p-4 text-center bg-white">
                    <div class="mt-3">
                        <button type="button" @click="triggerImageInput"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">Choisir
                            une image</button>
                        <input ref="imageInput" type="file" id="image" @change="handleFileUpload" class="hidden" />
                    </div>
                    <p v-if="form.image" class="text-sm text-gray-600 mt-2">Fichier sélectionné : {{ form.image.name }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" @click="goBack" class="px-4 py-2 rounded-md bg-gray-100">Annuler</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">{{
                    categorie ? 'Mettre à jour' : 'Créer' }}</button>
            </div>
        </div>
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
        triggerImageInput() {
            if (this.$refs.imageInput) this.$refs.imageInput.click();
        },
        removeImage() {
            this.form.image = null;
        },
        openInNewTab(src) {
            if (!src) return;
            window.open(src, '_blank');
        },
        submitForm() {
            const formData = new FormData();
            formData.append("nom", this.form.nom);
            if (this.form.image) {
                formData.append("image", this.form.image);
            }
            this.$emit("submit", formData);
        },

        goBack() {
            if (this.$router && this.$router.back) this.$router.back();
            else window.history.back();
        },
    },
};
</script>

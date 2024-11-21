<template>
    <form @submit.prevent="submitForm">
        <div class="mb-4">
            <label for="nom" class="block text-gray-700">Nom</label>
            <input
                v-model="formData.nom"
                type="text"
                id="nom"
                class="border rounded w-full py-2 px-3"
                placeholder="Nom de l'agent"
            />
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700">Image de l'agent</label>
            <input
                type="file"
                id="image"
                @change="handleImageChange"
                class="border rounded w-full py-2 px-3"
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
    name: "FormulaireAgent",
    props: {
        zone: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            formData: {
                nom: "",
                type: "personne",
                image: null,
            },
        };
    },
    watch: {
        zone: {
            immediate: true,

        },
    },
    methods: {
        handleImageChange(event) {
            this.formData.image = event.target.files[0];
        },
        submitForm() {
            const formData = new FormData();
            formData.append("nom", this.formData.nom);
            console.log(this.formData);
            if (this.formData.image) {
                formData.append("image", this.formData.image);
            }
            this.$emit("submit", formData);
        },
    },
};
</script>

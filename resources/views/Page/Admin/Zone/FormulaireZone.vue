<template>
    <form @submit.prevent="submitForm">
        <div class="mb-4">
            <label for="nom" class="block text-gray-700">Nom</label>
            <input
                v-model="formData.nom"
                type="text"
                id="nom"
                class="border rounded w-full py-2 px-3"
                placeholder="Nom de la zone"
            />
        </div>
        <div class="mb-4">
            <label for="type" class="block text-gray-700">Type</label>
            <select
                v-model="formData.type"
                id="type"
                class="border rounded w-full py-2 px-3"
            >
                <option value="personne">Personne</option>
                <option value="zone">Zone</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="image" class="block text-gray-700">Image de la zone</label>
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
    name: "FormulaireZone",
    props: {
        zone: {
            type: Object,
            default: null, // Assurez-vous que la prop est initialisée à null par défaut
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
        // Watcher pour mettre à jour `formData` quand la prop `zone` change
        zone: {
            immediate: true, // Exécute le watcher dès le montage du composant
            handler(newZone) {
                if (newZone) {
                    this.formData.nom = newZone.nom || "";
                    this.formData.type = newZone.type || "personne";
                }
            },
        },
    },
    methods: {
        handleImageChange(event) {
            this.formData.image = event.target.files[0];
        },
        submitForm() {
            const formData = new FormData();
            formData.append("nom", this.formData.nom);
            formData.append("type", this.formData.type);
            if (this.formData.image) {
                formData.append("image", this.formData.image);
            }
            this.$emit("submit", formData);
        },
    },
};
</script>

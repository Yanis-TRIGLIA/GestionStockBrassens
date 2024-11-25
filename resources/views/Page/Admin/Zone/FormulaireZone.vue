<template>

    <form @submit.prevent="submitForm">
        <div class="mb-4">
            <label for="nom" class="block text-gray-700">Nom</label>
            <input
                v-model="formData.nom"
                type="text"
                id="nom"
                class="border rounded w-full py-2 px-3"
                placeholder="Nom"
                value="{{$props.zone.nom}}"
            />
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700">Image de la {{$props.zone.type}}</label>
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
            required: false,
            default: () => ({
                nom: "",
                type: "zone",
                image_url: null,
            }),
        },
    },

    data() {
        return {
            formData: {
                nom: this.zone?.nom || "", // Initialize with a fallback
                type: this.zone?.type || "zone",
                image: null,
            },
        };
    },

    watch: {
        // Watcher pour mettre à jour `formData` quand la prop `zone` change
        zone: {
            immediate: true, // Exécute le watcher dès le montage du composant

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
            if (this.formData.type) {
                formData.append("type", this.formData.type);
            }
            this.$emit("submit", formData);
        },
    },
};
</script>

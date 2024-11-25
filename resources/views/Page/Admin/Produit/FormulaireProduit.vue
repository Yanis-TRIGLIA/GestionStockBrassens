<template>
    <form @submit.prevent="submitForm">
        <!-- Nom -->
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

        <!-- Description -->
        <div class="mb-4">
            <label for="description" class="block text-gray-700">Description</label>
            <textarea
                v-model="formData.description"
                id="description"
                class="border rounded w-full py-2 px-3"
                placeholder="Description du produit"
            ></textarea>
        </div>

        <!-- Quantité -->
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

        <!-- Catégories (Dropdown personnalisé) -->
        <div class="mb-4 relative">
            <label for="categories" class="block text-gray-700 mb-2">Catégories</label>

            <!-- Dropdown button -->
            <button
                @click="toggleDropdown"
                type="button"
                class="border rounded w-full py-2 px-3 text-left bg-white flex justify-between items-center"
            >
        <span>
          <span v-if="formData.categories.length === 0">Sélectionner des catégories</span>
          <span v-else>
            {{ selectedCategories.join(", ") }}
          </span>
        </span>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 transform"
                    :class="{ 'rotate-180': isDropdownOpen }"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path
                        fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd"
                    />
                </svg>
            </button>

            <!-- Dropdown content -->
            <div
                v-if="isDropdownOpen"
                class="absolute z-10 mt-2 w-full bg-white border border-gray-300 rounded shadow-md max-h-60 overflow-y-auto"
            >
                <div
                    v-for="categorie in categories"
                    :key="categorie.id"
                    class="px-4 py-2 flex items-center hover:bg-gray-100"
                >
                    <input
                        type="checkbox"
                        :value="categorie.id"
                        v-model="formData.categories"
                        class="mr-2"
                        id="categorie-{{ categorie.id }}"
                    />
                    <label :for="`categorie-${categorie.id}`" class="cursor-pointer">
                        {{ categorie.nom }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Image -->
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

        <!-- Fichier technique -->
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

        <!-- Bouton d'enregistrement -->
        <button
            type="submit"
            class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
        >
            Enregistrer
        </button>
    </form>
</template>

<script>
import axios from "../../../../../axios.config.js";

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
            isDropdownOpen: false, // État du menu déroulant
            formData: {
                nom: "",
                description: "",
                quantité: null,
                image: null,
                file_product: null,
                categories: [], // Catégories sélectionnées
            },
            categories: [], // Liste des catégories disponibles
        };
    },

    computed: {
        // Renvoie les noms des catégories sélectionnées
        selectedCategories() {
            return this.categories
                .filter((categorie) => this.formData.categories.includes(categorie.id))
                .map((categorie) => categorie.nom);
        },
    },

    watch: {
        produit: {
            immediate: true,
            handler(newProduit) {
                if (newProduit) {
                    this.formData = { ...newProduit };
                    // Vérifie que les catégories existent avant de mapper
                    this.formData.categories = Array.isArray(newProduit.categories)
                        ? newProduit.categories.map((c) => c.id)
                        : [];
                }
            },
        },
    },

    methods: {
        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
        },

        closeDropdown() {
            this.isDropdownOpen = false;
        },

        fetchCategories() {
            axios
                .get("/api/categorie")
                .then((response) => {
                    this.categories = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors du chargement des catégories :", error);
                });
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

            formData.append("categories", JSON.stringify(this.formData.categories));
            this.$emit("submit", formData);
        },
    },

    mounted() {
        this.fetchCategories();

        // Ferme le menu déroulant si on clique à l'extérieur
        document.addEventListener("click", (event) => {
            if (!this.$el.contains(event.target)) {
                this.closeDropdown();
            }
        });
    },

    beforeDestroy() {
        document.removeEventListener("click", this.closeDropdown);
    },
};
</script>

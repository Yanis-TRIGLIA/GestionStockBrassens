<template>
    <form @submit.prevent="submitForm">
        <!-- Nom -->
        <div class="mb-4">
            <label for="nom" class="block text-gray-700">Nom</label>
            <input v-model="formData.nom" type="text" id="nom" class="border rounded w-full py-2 px-3"
                placeholder="Nom du produit" />
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label for="description" class="block text-gray-700">Description</label>
            <textarea v-model="formData.description" id="description" class="border rounded w-full py-2 px-3"
                placeholder="Description du produit"></textarea>
        </div>

        <!-- Quantité -->
        <div class="mb-4">
            <label for="quantité" class="block text-gray-700">Quantité</label>
            <input v-model="formData.quantité" type="number" id="quantité" class="border rounded w-full py-2 px-3"
                placeholder="Quantité" />
        </div>

        <!-- Catégories (Dropdown personnalisé) -->
        <div class="mb-4 relative">
            <label for="categories" class="block text-gray-700 mb-2">Catégories</label>

            <!-- Dropdown button -->
            <button @click="toggleDropdown" type="button"
                class="border rounded w-full py-2 px-3 text-left bg-white flex justify-between items-center">
                <span>
                    <span v-if="formData.categories.length === 0">Sélectionner des catégories</span>
                    <span v-else>
                        {{ selectedCategories.join(", ") }}
                    </span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform"
                    :class="{ 'rotate-180': isDropdownOpen }" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Dropdown content -->
            <div v-if="isDropdownOpen"
                class="absolute z-10 mt-2 w-full bg-white border border-gray-300 rounded shadow-md max-h-60 overflow-y-auto">
                <div v-for="categorie in categories" :key="categorie.id"
                    class="px-4 py-2 flex items-center hover:bg-gray-100">
                    <input type="checkbox" :value="categorie.id" v-model="formData.categories" class="mr-2"
                        id="categorie-{{ categorie.id }}" />
                    <label :for="`categorie-${categorie.id}`" class="cursor-pointer">
                        {{ categorie.nom }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Fiches techniques -->
        <div class="mb-4">
            <label for="fiches_techniques" class="block text-gray-700">Fiches techniques (PDF)</label>
            <input type="file" id="fiches_techniques" multiple @change="handleFichesTechniquesChange"
                class="border rounded w-full py-2 px-3" accept=".pdf" />
            <!-- Liste des fichiers sélectionnés -->
            <div v-if="formData.fiches_techniques && formData.fiches_techniques.length > 0" class="mt-2">
                <p v-for="(file, index) in formData.fiches_techniques" :key="index" class="text-sm text-gray-600">
                    {{ file.name }}
                    <span @click="removeFile(index)" class="text-red-500 cursor-pointer ml-2">✖</span>
                </p>
            </div>
        </div>

        <div v-if="produit && produit.file_product" class="mt-2 mb-4 p-4 space-x-2">
            <router-link v-for="(file, index) in JSON.parse(produit.file_product)" :to="`/storage/${file}`"
                target="_blank">
                <p class="text-sm font-semibold underline text-gray-600">{{ file.slice(6, 50) }}</p>
            </router-link>
        </div>

        <!-- Image -->
        <div class="mb-4">
            <label for="image" class="block text-gray-700">Image du produit</label>
            <input type="file" id="image" @change="handleImageChange" class="border rounded w-full py-2 px-3"
                accept="image/*" />
            <div class="w-full flex justify-center">
                <img v-if="formData.image_url" class="text-sm text-gray-600 mt-2 w-1/2  h-96 rounded-lg"
                    :src="`${baseUrl}/storage/${produit.image_url}`" />
            </div>
        </div>

        <!-- Bouton d'enregistrement -->
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
            Enregistrer
        </button>
    </form>
</template>

<script>
import { Image } from "primevue";
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
            baseUrl: import.meta.env.VITE_APP_URL,
            formData: {
                nom: "",
                description: "",
                quantité: null,
                image: null,
                file_product: null,
                fiches_techniques: [],
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

        handleFichesTechniquesChange(event) {
            const files = Array.from(event.target.files);

            // Vérifie et initialise le tableau si nécessaire
            if (!Array.isArray(this.formData.fiches_techniques)) {
                this.formData.fiches_techniques = [];
            }

            // Ajoute les fichiers au tableau
            this.formData.fiches_techniques.push(...files);

            console.log("Fichiers ajoutés :", this.formData.fiches_techniques.map(file => file.name));
        },

        // Supprimer un fichier de la liste
        removeFile(index) {
            this.formData.fiches_techniques.splice(index, 1);
        },

        submitForm() {
            const formData = new FormData();
            formData.append("nom", this.formData.nom);
            formData.append("description", this.formData.description || ""); // Gestion du champ nullable
            formData.append("quantité", parseInt(this.formData.quantité, 10)); // Conversion en entier

            if (this.formData.fiches_techniques) {
                this.formData.fiches_techniques.forEach((file, index) => {
                    formData.append(`fiches_techniques[${index}]`, file);
                });
            }

            if (this.formData.image) {
                formData.append("image", this.formData.image);
            }

            if (this.formData.file_product) {
                formData.append("file_product", this.formData.file_product);
            }

            // Ajout des catégories (tableau compatible avec Laravel)
            this.formData.categories.forEach((categorieId, index) => {
                formData.append(`categories[${index}]`, categorieId);
            });

            this.$emit("submit", formData);
        }
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

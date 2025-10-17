<template>
    <form @submit.prevent="submitForm" class="max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: main inputs -->
                <div class="lg:col-span-2">
                    <h2 class="text-xl font-semibold mb-4">Informations produit</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input v-model="formData.nom" type="text" id="nom"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Nom du produit" />
                        </div>

                        <!-- Référence -->
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Référence</label>
                            <input v-model="formData.reference" type="text" id="reference"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Référence" />
                        </div>

                        <!-- Prix -->
                        <div>
                            <label for="prix" class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
                            <input v-model.number="formData.prix" type="number" step="0.01" min="0" id="prix"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Prix" />
                        </div>

                        <!-- Quantité -->
                        <div>
                            <label for="quantité" class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                            <input v-model.number="formData.quantité" type="number" id="quantité"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Quantité" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea v-model="formData.description" id="description" rows="4"
                            class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                            placeholder="Description du produit"></textarea>
                    </div>

                    <!-- Catégories (Dropdown personnalisé) -->
                    <div class="mt-4 relative">
                        <label for="categories" class="block text-sm font-medium text-gray-700 mb-1">Catégories</label>

                        <!-- Dropdown button -->
                        <button @click.stop="toggleDropdown" type="button"
                            class="w-full bg-white border border-gray-200 rounded-md px-3 py-2 text-left flex justify-between items-center">
                            <span class="text-sm text-gray-700">
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
                            class="absolute z-10 mt-2 w-full bg-white border border-gray-200 rounded-md shadow-md max-h-60 overflow-y-auto">
                            <div class="px-3 py-2">
                                <input ref="categorySearch" v-model="categoryQuery" type="search" placeholder="Rechercher une catégorie..." class="w-full rounded-md border border-gray-200 px-2 py-1 text-sm" @click.stop />
                            </div>
                            <div v-for="categorie in filteredCategories" :key="categorie.id"
                                class="px-4 py-2 flex items-center hover:bg-gray-50">
                                <input type="checkbox" :value="categorie.id" v-model="formData.categories" @click.stop class="mr-2" :id="`categorie-${categorie.id}`" />
                                <label @click.stop :for="`categorie-${categorie.id}`" class="cursor-pointer text-sm text-gray-700">
                                    {{ categorie.nom }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Fiches techniques (drag & drop) -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fiches techniques (PDF)</label>
                        <div @dragover.prevent @drop.prevent="onDropFiches" @dragenter.prevent class="border-dashed border-2 border-gray-200 rounded-md p-4 text-center bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex items-center justify-center gap-3">
                                <!-- Icon PDF -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <path d="M14 2v6h6" />
                                    <path d="M9 12h6" />
                                    <path d="M9 16h6" />
                                </svg>
                                <p class="text-sm text-gray-600">Glissez-déposez des fichiers PDF ici ou</p>
                            </div>
                            <button type="button" @click="triggerFichesInput" class="mt-2 inline-flex items-center px-3 py-2 bg-white border border-gray-200 rounded-md text-sm shadow-sm hover:bg-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 5v14" />
                                    <path d="M5 12h14" />
                                </svg>
                                Sélectionner des fichiers
                            </button>
                            <input ref="fichesInput" type="file" id="fiches_techniques" multiple class="hidden" accept=".pdf" @change="handleFichesTechniquesChange" />
                        </div>

                        <!-- Liste des fichiers sélectionnés -->
                        <div v-if="formData.fiches_techniques && formData.fiches_techniques.length > 0" class="mt-3 space-y-2">
                            <div v-for="(file, index) in formData.fiches_techniques" :key="index" class="flex items-center justify-between bg-white border border-gray-100 rounded-md px-3 py-2">
                                <div class="flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M8 2a2 2 0 00-2 2v12a2 2 0 002 2h4a2 2 0 002-2V6l-4-4H8z" />
                                    </svg>
                                    <div class="text-sm text-gray-700 truncate">{{ file.name }}</div>
                                </div>
                                <button type="button" @click="removeFile(index)" class="text-red-500 text-sm">Supprimer</button>
                            </div>
                        </div>

                        <!-- Fichiers déjà uploadés (pour édition) placés sous Fiches techniques -->
                        <div v-if="produit && produit.file_product" class="mt-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Fichiers déjà uploadés</h4>
                            <div class="space-y-2">
                                <div v-for="(file, index) in JSON.parse(produit.file_product)" :key="index" class="flex items-center justify-between bg-white border border-gray-100 rounded-md px-3 py-2">
                                    <a :href="`/${file}`" target="_blank" class="text-sm text-indigo-600 truncate">{{ file.split('/').pop() }}</a>
                                    <button type="button" @click="openDeleteFileModal(file)" class="text-red-500 hover:text-red-700 ml-3" aria-label="Supprimer fichier">
                                        <!-- cross icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M18 6L6 18" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: image dropzone + preview -->
                <div>
                    <h3 class="text-lg font-medium mb-2">Image principale</h3>
                    <div @dragover.prevent @drop.prevent="onDropImage" @dragenter.prevent class="border-dashed border-2 border-indigo-200 rounded-md p-4 text-center bg-white">
                        <div v-if="!previewImage" class="flex items-center justify-center gap-3 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5-5 5 5" />
                                <path d="M12 5v12" />
                            </svg>
                            <p class="text-sm text-gray-600">Glissez-déposez une image ici ou</p>
                        </div>
                        <div v-if="previewImage" class="mb-4">
                            <img :src="previewImage" alt="Preview" class="w-full h-48 object-cover rounded-md shadow-sm" />
                            <div class="flex justify-between mt-2">
                                <button type="button" @click="removeImage" class="px-3 py-2 bg-red-500 text-white rounded-md text-sm">Retirer</button>
                                <button type="button" @click="openInNewTab(previewImage)" class="px-3 py-2 bg-gray-100 rounded-md text-sm">Ouvrir</button>
                            </div>
                        </div>

                        <div v-else class="py-6">
                            <p class="text-sm text-gray-600">Glissez-déposez une image ici ou</p>
                            <div class="mt-3">
                                <button type="button" @click="triggerImageInput" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">Choisir une image</button>
                                <input ref="imageInput" type="file" id="image" @change="handleImageChange" class="hidden" accept="image/*" />
                            </div>
                        </div>
                    </div>

                    <!-- (duplicated block removed: fichiers déjà uploadés shown under Fiches techniques) -->
                </div>
            </div>

            <!-- Footer actions -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" @click="goBack" class="px-4 py-2 rounded-md bg-gray-100">Annuler</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Enregistrer</button>
            </div>
        </div>
    </form>

    <!-- Modal de confirmation suppression fichier -->
    <div v-if="showDeleteFileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-md p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-3">Confirmer la suppression</h3>
            <p class="text-sm text-gray-700">Voulez-vous vraiment supprimer ce fichier ?</p>
            <div class="mt-4 flex justify-end gap-3">
                <button @click="cancelDeleteFile" class="px-4 py-2 rounded bg-gray-100">Annuler</button>
                <button @click="confirmDeleteFile" class="px-4 py-2 rounded bg-red-600 text-white">Supprimer</button>
            </div>
        </div>
    </div>
</template>

<script>
import { Image } from "primevue";
import axios from "../../../../../axios.config.js";

export default {
    name: "FormulaireProduit",
    emits: ["submit"],

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
                prix: null,
                reference: "",
                quantité: null,
                image: null,
                file_product: null,
                fiches_techniques: [],
                categories: [], // Catégories sélectionnées
            },
            previewImage: null,
            categoryQuery: '',
            showDeleteFileModal: false,
            fileToDelete: null,
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
        filteredCategories() {
            if (!this.categoryQuery) return this.categories;
            const q = this.categoryQuery.toLowerCase();
            return this.categories.filter(c => c.nom.toLowerCase().includes(q));
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
                    // Si on édite un produit avec une image existante, afficher l'aperçu
                    if (newProduit.image_url) {
                        this.previewImage = `${this.baseUrl}/${newProduit.image_url}`;
                    } else {
                        this.previewImage = null;
                    }
                }
            },
        },
    },

    methods: {
        toggleDropdown() {
            this.isDropdownOpen = !this.isDropdownOpen;
            this.$nextTick(() => {
                if (this.isDropdownOpen && this.$refs.categorySearch) {
                    this.$refs.categorySearch.focus();
                }
            });
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
                // Créer un aperçu en base64
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(file);
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

        // Trigger inputs
        triggerImageInput() {
            if (this.$refs.imageInput) this.$refs.imageInput.click();
        },
        triggerFichesInput() {
            if (this.$refs.fichesInput) this.$refs.fichesInput.click();
        },

        // Drop handlers
        onDropImage(event) {
            const files = event.dataTransfer.files;
            if (files && files.length) {
                const file = files[0];
                this.formData.image = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        onDropFiches(event) {
            const files = Array.from(event.dataTransfer.files || []);
            if (!Array.isArray(this.formData.fiches_techniques)) this.formData.fiches_techniques = [];
            // Garde uniquement les PDF
            const pdfs = files.filter(f => f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf'));
            this.formData.fiches_techniques.push(...pdfs);
        },

        removeImage() {
            this.formData.image = null;
            this.previewImage = null;
        },

        // Delete uploaded existing file (confirmation)
        openDeleteFileModal(file) {
            this.fileToDelete = file;
            this.showDeleteFileModal = true;
        },
        cancelDeleteFile() {
            this.fileToDelete = null;
            this.showDeleteFileModal = false;
        },
        
        confirmDeleteFile() {
            // If produit exists (edit mode), try to call API to delete server-side file
            if (this.produit && this.produit.id) {
                axios.post(`/api/produits/${this.produit.id}/delete-file`, { file: this.fileToDelete })
                    .then(() => {
                        // Remove from local produit.file_product array
                        const arr = JSON.parse(this.produit.file_product || '[]').filter(f => f !== this.fileToDelete);
                        this.produit.file_product = JSON.stringify(arr);
                        this.showSuccessToast('Fichier supprimé');
                    })
                    .catch(() => {
                        this.showErrorToast('Impossible de supprimer le fichier côté serveur');
                    })
                    .finally(() => {
                        this.cancelDeleteFile();
                    });
            } else {
                // If produit not saved yet, just remove from displayed list by filtering out
                const files = JSON.parse(this.produit.file_product || '[]').filter(f => f !== this.fileToDelete);
                this.produit.file_product = JSON.stringify(files);
                this.cancelDeleteFile();
            }
        },

        openInNewTab(src) {
            if (!src) return;
            window.open(src, '_blank');
        },

        goBack() {
            if (this.$router && this.$router.back) this.$router.back();
            else window.history.back();
        },

        // Supprimer un fichier de la liste
        removeFile(index) {
            this.formData.fiches_techniques.splice(index, 1);
        },

        submitForm() {
            const formData = new FormData();
            formData.append("nom", this.formData.nom);
            formData.append("description", this.formData.description || ""); // Gestion du champ nullable
            formData.append("prix", parseFloat(this.formData.prix)); // Conversion en nombre flottant
            formData.append("reference", this.formData.reference); // Gestion du champ nullable
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

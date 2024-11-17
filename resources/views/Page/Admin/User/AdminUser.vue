<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Liste des Produits</h2>
        <button
            @click="$router.push('/admin/produits/create')"
            class="mb-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
            Créez votre produit
        </button>

        <table class="min-w-full border-collapse border border-gray-300">
            <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">ID</th>
                <th class="border border-gray-300 px-4 py-2">Nom</th>
                <th class="border border-gray-300 px-4 py-2">Quantité</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="produit in produits" :key="produit.id">
                <td class="border border-gray-300 px-4 py-2">{{ produit.id }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ produit.nom }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ produit.quantité }}</td>
                <td class="border border-gray-300 px-4 py-2 flex space-x-4">
                    <button
                        @click="$router.push(`/admin/produits/edit/${produit.id}`)"
                        class="text-blue-600"
                    >
                        Modifier
                    </button>
                    <button
                        @click="supprimerProduit(produit.id)"
                        class="text-red-600"
                    >
                        Supprimer
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import FormulaireProduit from "./FormulaireProduit.vue";

export default {
    name: "AdminUser",
    components: {FormulaireProduit},
    data() {
        return {
            produits: [],
            isFormVisible: false,
            isEditMode: false,
            currentProduit: null,
        };
    },
    methods: {
        openCreateForm() {
            this.isFormVisible = true;
            this.isEditMode = false;
            this.currentProduit = {
                nom: "",
                description: "",
                quantité: 0,
                image_url: "",
                file_product: null,
            };
        },
        openEditForm(produit) {
            this.isFormVisible = true;
            this.isEditMode = true;
            this.currentProduit = {...produit};
        },
        closeForm() {
            this.isFormVisible = false;
            this.currentProduit = null;
        },
        handleFormSubmit(formData) {
            if (this.isEditMode) {
                axios
                    .put(`/api/produits/${formData.id}`, formData)
                    .then(() => {
                        this.showSuccessToast("Produit modifié avec succès !");
                        this.fetchProduits();
                        this.closeForm();
                    })
                    .catch(() => {
                        this.showErrorToast("Erreur lors de la modification du produit.");
                    });
            } else {
                axios
                    .post("/api/produits", formData)
                    .then(() => {
                        this.showSuccessToast("Produit créé avec succès !");
                        this.fetchProduits();
                        this.closeForm();
                    })
                    .catch(() => {
                        this.showErrorToast("Erreur lors de la création du produit.");
                    });
            }
        },
        supprimerProduit(id) {
            axios
                .delete(`/api/produits/${id}`)
                .then(() => {
                    this.showSuccessToast("Produit supprimé avec succès !");
                    this.fetchProduits();
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la suppression du produit.");
                });
        },
        fetchProduits() {
            axios
                .get("/api/produits")
                .then((response) => {
                    this.produits = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des produits:", error);
                });
        },
        showSuccessToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#4CAF50",
                stopOnFocus: true,
            }).showToast();
        },
        showErrorToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#F44336",
                stopOnFocus: true,
            }).showToast();
        },
    },
    mounted() {
        this.fetchProduits();
    },
};
</script>

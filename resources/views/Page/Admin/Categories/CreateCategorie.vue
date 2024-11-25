<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Créer une Nouvelle Catégorie</h2>
        <FormulaireCategory @submit="createCategory" />
    </div>
</template>

<script>
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import FormulaireCategory from "./FormulaireCategorie.vue";

export default {
    name: "CreateCategorie",
    components: {
        FormulaireCategory,
    },
    methods: {
        createCategory(formData) {
            axios
                .post("/api/categorie", formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })
                .then(() => {
                    this.showSuccessToast("Catégorie créée avec succès !");
                    this.$router.push("/admin/categories");
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la création de la catégorie.");
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
};
</script>

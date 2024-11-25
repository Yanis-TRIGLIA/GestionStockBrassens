<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Modifier la Catégorie</h2>
        <FormulaireCategory :categorie="categorie" @submit="updateCategory" />
    </div>
</template>

<script>
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import FormulaireCategory from "./FormulaireCategorie.vue";

export default {
    name: "EditCategorie",
    components: {
        FormulaireCategory,
    },
    data() {
        return {
            categorie: null,
        };
    },
    methods: {
        fetchCategory() {
            axios
                .get(`/api/categorie/${this.$route.params.id}`)
                .then((response) => {
                    this.categorie = response.data;
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la récupération de la catégorie.");
                });
        },
        updateCategory(formData) {
            // Vérifiez si formData est déjà une instance de FormData
            if (!(formData instanceof FormData)) {
                const newFormData = new FormData();
                newFormData.append("name", formData.name);
                if (formData.image) {
                    newFormData.append("image", formData.image);
                }
                formData = newFormData; // Remplacez par l'instance FormData
            }

            axios
                .post(`/api/categorie/${this.$route.params.id}?_method=POST`, formData, {
                    headers: { "Content-Type": "multipart/form-data" },
                })
                .then(() => {
                    this.showSuccessToast("Catégorie mise à jour avec succès !");
                    this.$router.push("/admin/categories");
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la mise à jour de la catégorie.");
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
        this.fetchCategory();
    },
};
</script>

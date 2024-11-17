<template>
    <div>
        <FormulaireProduit
            :produit="produit"
            isEditMode
            @submit="updateProduit"
        />
    </div>
</template>

<script>
import axios from "../../../../../axios.config.js";
import Toastify from "toastify-js";
import FormulaireProduit from "./FormulaireProduit.vue";

export default {
    name: "EditProduit",
    components: { FormulaireProduit },
    data() {
        return {
            produit: null,
        };
    },
    methods: {
        fetchProduit() {
            const id = this.$route.params.id;
            axios
                .get(`/api/produits/${id}`)
                .then((response) => {
                    this.produit = response.data;
                    console.log(response.data)
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors du chargement du produit.");
                });
        },
        updateProduit(formData) {
            const id = this.$route.params.id;
            console.log("Updating product with ID:", id);

            // Convertir formData en un objet JSON
            const jsonData = {
                nom: formData.get("nom"),
                description: formData.get("description"),
                quantité: formData.get("quantité"),
            };

            // Si tu as des fichiers, tu peux les ajouter à la requête autrement
            //if (formData.get("image")) {
              //  jsonData.image = formData.get("image");
            //}


            axios
                .put(`/api/produits/${id}`, jsonData)
                .then((response) => {
                    this.produit = response.data;
                    this.showSuccessToast("Produit modifié avec succès !");
                    this.$router.push("/admin/produits");
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors de la modification du produit.");
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
        this.fetchProduit();
    },
};
</script>

<style scoped>
/* Ajoutez vos styles ici */
</style>

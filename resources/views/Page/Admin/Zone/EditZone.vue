<template>
    <div>
        <div v-if="loading" class="text-gray-500">Chargement des données...</div>

        <div v-else>
            <h2 class="text-2xl font-bold mb-4">Modifier {{ zone.nom }}</h2>
            <FormulaireZone
                :zone="zone"
                @submit="handleSubmit"
            />
        </div>
    </div>
</template>

<script>
import FormulaireZone from "./FormulaireZone.vue";
import axios from "../../../../../axios.config.js";

export default {
    name: "EditZone",
    components: { FormulaireZone },
    data() {
        return {
            zone: null,
            loading: true,
        };
    },
    methods: {
        fetchZone() {
            axios.get(`/api/zones/${this.$route.params.id}`)
                .then(response => {
                    this.zone = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    console.error("Erreur lors de la récupération de la zone :", error);
                    this.loading = false;

                    // Optionnel : afficher un message d'erreur à l'utilisateur
                    this.$toast.error("Erreur lors de la récupération de la zone.");
                });
        },
        handleSubmit(formData) {
            const jsonData = {
                nom: formData.get("nom"),
                type: formData.get("type"),
            };
            axios.put(`/api/zones/${this.$route.params.id}`, jsonData)
                .then(() => {
                    this.$router.push("/admin/zones");
                })
                .catch(error => {
                    console.error("Erreur lors de la modification de la zone :", error);

                    // Optionnel : afficher un message d'erreur à l'utilisateur
                    this.$toast.error("Erreur lors de la modification de la zone.");
                });
        },
    },
    mounted() {
        this.fetchZone();
    },
};
</script>

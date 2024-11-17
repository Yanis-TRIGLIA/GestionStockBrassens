<template>
    <div>
        <h2 class="text-2xl font-bold mb-4">Modifier une zone</h2>
        <FormulaireZone
            :zone="zone"
            @submit="handleSubmit"
        />
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
        };
    },
    methods: {
        fetchZone() {
            axios.get(`/api/zones/${this.$route.params.id}`)
                .then(response => {
                    this.zone = response.data;
                })
                .catch(error => {
                    console.error("Erreur lors de la récupération de la zone :", error);
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
                });
        },
    },
    mounted() {
        this.fetchZone();
    },
};
</script>

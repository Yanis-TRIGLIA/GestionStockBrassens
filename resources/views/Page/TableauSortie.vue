<script>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import { ref, onMounted } from 'vue';
import axios from "../../../axios.config.js";

export default {
    name: "TableauSortie",
    setup() {
        const sorties = ref([]);

        onMounted(() => {
            axios
                .get(`/api/sorties`)
                .then((response) => {
                    sorties.value = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des sorties:", error);
                });
        });

        // Formater le prix (si nécessaire pour une future colonne, sinon peut être supprimé)
        const formatCurrency = (value) => {
            return value.toLocaleString('en-US', { style: 'currency', currency: 'USD' });
        };

        return { sorties, formatCurrency };
    }
};
</script>

<template>
    <div class="card p-4">
        <DataTable :value="sorties" tableStyle="min-width: 50rem border">
            <template #header>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xl font-bold">Sortie de Produits</span>
                    <Button icon="pi pi-refresh" rounded raised />
                </div>
            </template>

            <!-- Nom du produit -->
            <Column field="produit.nom" header="Nom du Produit" :sortable="true"></Column>

            <!-- Image du produit -->
            <Column header="Image">
                <template #body="slotProps">
                    <img :src="slotProps.data.produit.image_url" :alt="slotProps.data.produit.nom" class="w-24 rounded" />
                </template>
            </Column>

            <!-- Quantité sortie -->
            <Column field="quantité" header="Quantité Sortie" :sortable="true"></Column>

            <!-- Date de sortie -->
            <Column field="date_sortie" header="Date de Sortie" :sortable="true">
                <template #body="slotProps">
                    {{ new Date(slotProps.data.date_sortie).toLocaleString() }}
                </template>
            </Column>



            <!-- Stock actuel du produit -->
            <Column field="produit.quantité" header="Stock Actuel" :sortable="true"></Column>


            <!-- Date de sortie -->
            <Column field="zone.nom" header="Zone concernée" :sortable="true">
            </Column>

            <template #footer>
            </template>
        </DataTable>
    </div>
</template>

<style scoped>
/* Style optionnel pour améliorer la présentation */
.w-24 {
    width: 6rem;
}
</style>

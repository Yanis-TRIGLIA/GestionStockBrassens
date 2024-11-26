<template>
    <div class="p-6 bg-gray-50 rounded shadow-lg">
        <!-- En-tête et contrôles -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-700">Sortie de Produits</h1>
            <div class="flex space-x-2">
                <!-- Input de recherche -->
                <input
                    v-model="searchQuery"
                    @input="filterData"
                    type="text"
                    placeholder="Rechercher un produit..."
                    class="px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300"
                />
                <!-- Bouton Actualiser -->
                <button
                    @click="refreshData"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center space-x-2"
                >
                    <i class="pi pi-refresh"></i>
                    <span>Actualiser</span>
                </button>
                <!-- Bouton Exporter en CSV -->
                <button
                    @click="exportToCSV"
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 flex items-center space-x-2"
                >
                    <i class="pi pi-download"></i>
                    <span>Exporter en CSV</span>
                </button>
                <!-- Bouton Imprimer -->
                <button
                    @click="printTable"
                    class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 flex items-center space-x-2"
                >
                    <i class="pi pi-print"></i>
                    <span>Imprimer</span>
                </button>
            </div>
        </div>

        <!-- Tableau -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                <!-- En-tête des colonnes avec tri -->
                <thead class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.key"
                            @click="sortData(col.key)"
                            class="px-6 py-3 border-b text-left cursor-pointer hover:bg-gray-300"
                        >
                            {{ col.label }}
                            <span v-if="sortColumn === col.key">
                                <i :class="sortOrder === 'asc' ? 'pi pi-sort-amount-up' : 'pi pi-sort-amount-down'"></i>
                            </span>
                        </th>
                    </tr>
                </thead>
                <!-- Corps du tableau -->
                <tbody>
                    <tr
                        v-for="sortie in filteredAndSortedData"
                        :key="sortie.id"
                        class="even:bg-gray-100 hover:bg-gray-50"
                    >
                        <td class="px-6 py-4 border-b text-gray-700">{{ sortie.produit.nom }}</td>
                        <td class="px-6 py-4 border-b">
                            <img
                                v-if="sortie.produit.image_url"
                                :src="`http://127.0.0.1:8000/storage/${sortie.produit.image_url}`"
                                alt="Image du produit"
                                class="w-16 h-16 object-cover rounded"
                            />
                        </td>
                        <td class="px-6 py-4 border-b text-gray-700">{{ sortie.quantité }}</td>
                        <td class="px-6 py-4 border-b text-gray-700">
                            {{ new Date(sortie.updated_at).toLocaleString() }}
                        </td>
                        <td class="px-6 py-4 border-b text-gray-700">{{ sortie.number_after_reduce }}</td>

                        <!-- Zone Concernée, affichage conditionnel -->
                        <td class="px-6 py-4 border-b text-gray-700">
                            {{ sortie.zone.type === 'zone' ? sortie.zone.nom : 'Aucun' }}
                        </td>

                        <!-- Personne Concernée, affichage conditionnel -->
                        <td class="px-6 py-4 border-b text-gray-700">
                            {{ sortie.zone.type === 'personne' ? sortie.zone.nom : 'Aucun' }}
                        </td>

                        <!-- Observation -->
                        <td class="px-6 py-4 border-b text-gray-700">{{ sortie.observation || 'Aucune' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import axios from "../../../axios.config.js";

export default {
    name: "TableauSortie",
    setup() {
        const sorties = ref([]);
        const searchQuery = ref("");
        const sortColumn = ref("");
        const sortOrder = ref("asc");

        const columns = [
            { key: "produit.nom", label: "Nom du Produit" },
            { key: "produit.image_url", label: "Image" },
            { key: "quantité", label: "Quantité Sortie" },
            { key: "updated_at", label: "Date de Sortie" },
            { key: "number_after_reduce", label: "Stock Actuel" },
            { key: "zone.nom", label: "Zone Concernée" },
            { key: "zone.nom", label: "Personne Concernée" },
            { key: "observation", label: "Observation" } // Nouvelle colonne Observation
        ];

        const columnscsv = [
            { key: "produit.nom", label: "Nom du Produit" },
            { key: "quantité", label: "Quantité Sortie" },
            { key: "updated_at", label: "Date de Sortie" },
            { key: "number_after_reduce", label: "Stock Actuel" },
            { key: "zone.nom", label: "Zone Concernée" },
            { key: "zone.nom", label: "Personne Concernée" },
            { key: "observation", label: "Observation" } // Nouvelle colonne Observation pour CSV
        ];

        const fetchData = () => {
            axios
                .get(`/api/sorties`)
                .then((response) => {
                    sorties.value = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des sorties:", error);
                });
        };

        const refreshData = () => {
            fetchData();
        };

        const filteredAndSortedData = computed(() => {
            let data = sorties.value;

            // Filtrage par recherche
            if (searchQuery.value) {
                data = data.filter((sortie) =>
                    sortie.produit.nom
                        .toLowerCase()
                        .includes(searchQuery.value.toLowerCase())
                );
            }

            // Tri par colonne
            if (sortColumn.value) {
                data.sort((a, b) => {
                    const aValue = sortColumn.value
                        .split(".")
                        .reduce((o, i) => o[i], a);
                    const bValue = sortColumn.value
                        .split(".")
                        .reduce((o, i) => o[i], b);

                    if (aValue < bValue) return sortOrder.value === "asc" ? -1 : 1;
                    if (aValue > bValue) return sortOrder.value === "asc" ? 1 : -1;
                    return 0;
                });
            }

            return data;
        });

        const sortData = (column) => {
            if (sortColumn.value === column) {
                // Inverser l'ordre de tri
                sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
            } else {
                sortColumn.value = column;
                sortOrder.value = "asc";
            }
        };

        const exportToCSV = () => {
            const headers = columnscsv.map((col) => col.label);
            const rows = filteredAndSortedData.value.map((sortie) => [
                sortie.produit.nom,
                sortie.quantité,
                new Date(sortie.updated_at).toLocaleString(),
                sortie.number_after_reduce,
                sortie.zone.type === 'zone' ? sortie.zone.nom : 'Aucun',  // Zone Concernée
                sortie.zone.type === 'personne' ? sortie.zone.nom : 'Aucun',  // Personne Concernée
                sortie.observation || 'Aucune'  // Observation
            ]);

            const csvContent =
                "data:text/csv;charset=utf-8," +
                [headers.join(","), ...rows.map((row) => row.join(","))].join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "sorties.csv");
            document.body.appendChild(link);

            link.click();
            document.body.removeChild(link);
        };

        const printTable = () => {
            const printContent = `
                <table border="1" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Nom du Produit</th>
                            <th>Quantité Sortie</th>
                            <th>Date de Sortie</th>
                            <th>Stock Actuel</th>
                            <th>Zone Concernée</th>
                            <th>Personne Concernée</th>
                            <th>Observation</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filteredAndSortedData.value.map(sortie => `
                            <tr>
                                <td>${sortie.produit.nom}</td>
                                <td>${sortie.quantité}</td>
                                <td>${new Date(sortie.updated_at).toLocaleString()}</td>
                                <td>${sortie.number_after_reduce}</td>
                                <td>${sortie.zone.type === 'zone' ? sortie.zone.nom : 'Aucun'}</td>
                                <td>${sortie.zone.type === 'personne' ? sortie.zone.nom : 'Aucun'}</td>
                                <td>${sortie.observation || 'Aucune'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            const printWindow = window.open("", "_blank");
            printWindow.document.write(printContent);
            printWindow.document.close();
            printWindow.print();
        };

        onMounted(() => {
            fetchData();
        });

        return {
            sorties,
            searchQuery,
            refreshData,
            exportToCSV,
            filteredAndSortedData,
            sortData,
            columns,
            sortColumn,
            sortOrder,
            printTable,
        };
    },
};
</script>

<style scoped>
img {
    transition: transform 0.2s ease-in-out;
}
img:hover {
    transform: scale(1.1);
}
@media print {
    img {
        display: none; /* Masquer les images lors de l'impression */
    }
}
</style>

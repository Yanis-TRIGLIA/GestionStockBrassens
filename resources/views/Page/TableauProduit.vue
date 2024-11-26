<template>
    <div class="p-6 bg-gray-50 rounded shadow-lg">
        <!-- En-tête et contrôles -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-700">Tableau des produits</h1>
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
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Actualiser
                </button>
                <!-- Bouton Exporter en CSV -->
                <button
                    @click="exportToCSV"
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                >
                    Exporter en CSV
                </button>
                <!-- Bouton Imprimer -->
                <button
                    @click="printTable"
                    class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600"
                >
                    Imprimer
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
                        v-for="produit in filteredAndSortedData"
                        :key="produit.id"
                        class="even:bg-gray-100 hover:bg-gray-50"
                    >
                        <td class="px-6 py-4 border-b text-gray-700">{{ produit.nom }}</td>
                        <td class="px-6 py-4 border-b">
                            <img
                                v-if="produit.image_url"
                                :src="`http://127.0.0.1:8000/storage/${produit.image_url}`"
                                alt="Image du produit"
                                class="w-16 h-16 object-cover rounded"
                            />
                        </td>
                        <td class="px-6 py-4 border-b text-gray-700">{{ produit.quantité }}</td>
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
    name: "TableauProduit",
    setup() {
        const produits = ref([]);
        const searchQuery = ref("");
        const sortColumn = ref("");
        const sortOrder = ref("asc");

        const columns = [
            { key: "nom", label: "Nom du Produit" },
            { key: "image_url", label: "Image" },
            { key: "quantité", label: "Quantité produit" },
        ];

        const columnscsv = [
            { key: "nom", label: "Nom du Produit" },
            { key: "quantité", label: "Quantité produit" },
        ];

        const fetchData = () => {
            axios
                .get(`/api/produits`)
                .then((response) => {
                    produits.value = response.data;
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des produits:", error);
                });
        };

        const refreshData = () => {
            fetchData();
        };

        const filteredAndSortedData = computed(() => {
            let data = produits.value;

            // Filtrage par recherche
            if (searchQuery.value) {
                data = data.filter((produit) =>
                    produit.nom
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
            const rows = filteredAndSortedData.value.map((produit) => [
                produit.nom,
                produit.quantité,
            ]);

            const csvContent =
                "data:text/csv;charset=utf-8," +
                [headers.join(","), ...rows.map((row) => row.join(","))].join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "produits.csv");
            document.body.appendChild(link);

            link.click();
            document.body.removeChild(link);
        };

        const printTable = () => {
            const printContent = `
                <table border="1" style="border-collapse: collapse; width: 100%;">
                    <thead>
                        <tr>
                            <th>Nom du Produit</th>
                            <th>Quantité</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filteredAndSortedData.value
                            .map((produit) => {
                                return `
                                    <tr>
                                        <td>${produit.nom}</td>
                                        <td>${produit.quantité}</td>
                                    </tr>
                                `;
                            })
                            .join("")}
                    </tbody>
                </table>
            `;
            const win = window.open("", "", "width=900,height=700");
            win.document.write("<html><head><title>Impression du Tableau</title></head><body>");
            win.document.write(printContent);
            win.document.write("</body></html>");
            win.document.close();
            win.print();
        };

        onMounted(() => {
            fetchData();
        });

        return {
            produits,
            searchQuery,
            refreshData,
            exportToCSV,
            printTable,
            filteredAndSortedData,
            sortData,
            columns,
            sortColumn,
            sortOrder,
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
</style>

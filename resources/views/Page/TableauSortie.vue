<template>
    <div class="p-6 bg-gray-50 rounded shadow-lg">

        <!-- Pop-up de confirmation -->
        <div v-if="showDeletePopup" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded shadow-lg w-1/3">
                <h2 class="text-xl font-semibold mb-4">Confirmation</h2>
                <p class="text-gray-700 mb-6">
                    Êtes-vous sûr de vouloir supprimer cette sortie ?
                </p>
                <div class="flex justify-end space-x-4">
                    <button @click="cancelDelete"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Annuler</button>
                    <button @click="deleteSortie" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>


        <!-- En-tête et contrôles -->
        <div class="md:flex items-center justify-between mb-4">
            <h1 class="text-2xl font-bold text-gray-700">Sortie de Produits</h1>
            <div class="md:flex space-x-2">
                <!-- Input de recherche -->
                <input v-model="searchQuery" @input="filterData" type="text" placeholder="Rechercher un produit..."
                    class="px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300" />
                <!-- Bouton Actualiser -->
                <button @click="refreshData"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center md:mt-0 md:mb-0 mt-2 mb-2 ">
                    <i class="pi pi-refresh"></i>
                    <span>Actualiser</span>
                </button>
                <!-- Bouton Exporter en CSV -->
                <button @click="exportToCSV"
                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 flex items-center md:mt-0 md:mb-0 mt-2 mb-2">
                    <i class="pi pi-download"></i>
                    <span>Exporter en CSV</span>
                </button>
                <!-- Bouton Imprimer -->
                <button @click="printTable"
                    class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 flex items-center md:mt-0 md:mb-0 mt-2 mb-2">
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
                        <th v-for="col in columns" :key="col.key" @click="sortData(col.key)"
                            class="px-6 py-3 border-b text-left cursor-pointer hover:bg-gray-300">
                            {{ col.label }}
                            <span v-if="sortColumn === col.key">
                                <i :class="sortOrder === 'asc' ? 'pi pi-sort-amount-up' : 'pi pi-sort-amount-down'"></i>
                            </span>
                        </th>
                    </tr>
                </thead>
                <!-- Corps du tableau -->
                <tbody>
                    <tr v-for="sortie in filteredAndSortedData" :key="sortie.id"
                        class="even:bg-gray-100 hover:bg-gray-50">
                        <td class="px-6 py-4 border-b text-gray-700">{{ sortie.produit.nom }}</td>
                        <td class="px-6 py-4 border-b">
                            <img v-if="sortie.produit.image_url" :src="`${baseUrl}/${sortie.produit.image_url}`"
                                alt="Image du produit" class="w-16 h-16 object-cover rounded" />
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

                        <!-- A effectuer la sortie, affichage conditionnel -->
                        <td class="px-6 py-4 border-b text-gray-700">
                            {{ sortie.personne.nom }}
                        </td>

                        <!-- Observation -->
                        <td class="px-6 py-4 border-b text-gray-700">{{ sortie.observation || 'Aucune' }}</td>

                        <td><a @click="confirmDelete(sortie)" class="cursor-pointer"><svg class="w-full text-center "
                                    xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50"
                                    viewBox="0 0 100 100">
                                    <path fill="#8b8bc1"
                                        d="M77,32L77,32H23h-5v5l5,5v33.308C23,82.288,28.295,86,34.766,86h30.269	C71.616,86,77,82.288,77,75.308V41.917L82,37v-5H77z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M65.035,87H34.766C27.13,87,22,82.301,22,75.308V42.414l-4.707-4.707C17.105,37.52,17,37.265,17,37	v-5c0-0.552,0.448-1,1-1h64c0.552,0,1,0.448,1,1v5c0,0.268-0.108,0.525-0.299,0.713L78,42.336v32.972C78,82.301,72.79,87,65.035,87z M19,36.586l4.707,4.707C23.895,41.48,24,41.735,24,42v33.308C24,82.461,29.8,85,34.766,85h30.269C70.093,85,76,82.461,76,75.308	V41.917c0-0.268,0.108-0.525,0.299-0.713L81,36.581V33H19V36.586z">
                                    </path>
                                    <path fill="#6869ad"
                                        d="M29.5,60.5V42.917c0-1.375,1.125-2.5,2.5-2.5l0,0c1.375,0,2.5,1.125,2.5,2.5v16.75v14.25	c0,1.375-1.125,2.5-2.5,2.5l0,0c-1.375,0-2.5-1.125-2.5-2.5V70.5V60.5z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M32,76.917c-1.654,0-3-1.346-3-3V70.5c0-0.276,0.224-0.5,0.5-0.5s0.5,0.224,0.5,0.5v3.417	c0,1.103,0.897,2,2,2s2-0.897,2-2v-31c0-1.103-0.897-2-2-2s-2,0.897-2,2V60.5c0,0.276-0.224,0.5-0.5,0.5S29,60.776,29,60.5V42.917	c0-1.654,1.346-3,3-3s3,1.346,3,3v31C35,75.571,33.654,76.917,32,76.917z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M29.5,66c-0.276,0-0.5-0.224-0.5-0.5v-3c0-0.276,0.224-0.5,0.5-0.5s0.5,0.224,0.5,0.5v3	C30,65.776,29.776,66,29.5,66z">
                                    </path>
                                    <path fill="#6869ad"
                                        d="M44,76.5L44,76.5c-1.375,0-2.5-1.125-2.5-2.5V43c0-1.375,1.125-2.5,2.5-2.5l0,0	c1.375,0,2.5,1.125,2.5,2.5v31C46.5,75.375,45.375,76.5,44,76.5z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M44,77c-1.654,0-3-1.346-3-3V43c0-1.654,1.346-3,3-3s3,1.346,3,3v31C47,75.654,45.654,77,44,77z M44,41c-1.103,0-2,0.897-2,2v31c0,1.103,0.897,2,2,2s2-0.897,2-2V43C46,41.897,45.103,41,44,41z">
                                    </path>
                                    <path fill="#6869ad"
                                        d="M56,76.5L56,76.5c-1.375,0-2.5-1.125-2.5-2.5V43c0-1.375,1.125-2.5,2.5-2.5l0,0	c1.375,0,2.5,1.125,2.5,2.5v31C58.5,75.375,57.375,76.5,56,76.5z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M56,77c-1.654,0-3-1.346-3-3V43c0-1.654,1.346-3,3-3s3,1.346,3,3v31C59,75.654,57.654,77,56,77z M56,41c-1.103,0-2,0.897-2,2v31c0,1.103,0.897,2,2,2s2-0.897,2-2V43C58,41.897,57.103,41,56,41z">
                                    </path>
                                    <path fill="#6869ad"
                                        d="M68,76.5L68,76.5c-1.375,0-2.5-1.125-2.5-2.5V43c0-1.375,1.125-2.5,2.5-2.5l0,0	c1.375,0,2.5,1.125,2.5,2.5v31C70.5,75.375,69.375,76.5,68,76.5z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M68,77c-1.654,0-3-1.346-3-3V43c0-1.654,1.346-3,3-3s3,1.346,3,3v31C71,75.654,69.654,77,68,77z M68,41c-1.103,0-2,0.897-2,2v31c0,1.103,0.897,2,2,2s2-0.897,2-2V43C70,41.897,69.103,41,68,41z">
                                    </path>
                                    <path fill="#a3a3cd"
                                        d="M82,32v-0.237C82,24.749,79.127,21,70.727,21H29.273C20.873,21,18,24.749,18,31.763V32H82z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M82,33H18c-0.552,0-1-0.448-1-1v-0.237C17,23.628,20.785,20,29.272,20h41.455	C79.215,20,83,23.628,83,31.763V32C83,32.552,82.552,33,82,33z M19.013,31h61.975c-0.216-6.38-3.237-9-10.26-9H29.272	C22.25,22,19.229,24.62,19.013,31z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M81.5,36h-63c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h63c0.276,0,0.5,0.224,0.5,0.5	S81.776,36,81.5,36z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M75.5,82h-50c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h50c0.276,0,0.5,0.224,0.5,0.5	S75.776,82,75.5,82z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M63,22H37c-0.552,0-1-0.448-1-1v-2.646C36,15.401,38.309,13,41.146,13h17.708	C61.691,13,64,15.401,64,18.354V21C64,21.552,63.552,22,63,22z M38,20h24v-1.646C62,16.504,60.589,15,58.854,15H41.146	C39.411,15,38,16.504,38,18.354V20z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M72.5,29h-54c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h54c0.276,0,0.5,0.224,0.5,0.5	S72.776,29,72.5,29z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M77,58.917c-0.552,0-1-0.448-1-1v-12c0-0.552,0.448-1,1-1c2.757,0,5,2.243,5,5v4	C82,56.673,79.757,58.917,77,58.917z M78,47.088v9.657c1.164-0.413,2-1.525,2-2.829v-4C80,48.613,79.164,47.5,78,47.088z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M23,58.917c-2.757,0-5-2.243-5-5v-4c0-2.757,2.243-5,5-5c0.552,0,1,0.448,1,1v12	C24,58.469,23.552,58.917,23,58.917z M22,47.088c-1.164,0.413-2,1.525-2,2.829v4c0,1.304,0.836,2.416,2,2.829V47.088z">
                                    </path>
                                    <path fill="#1f212b"
                                        d="M78.5,29h-3c-0.276,0-0.5-0.224-0.5-0.5s0.224-0.5,0.5-0.5h3c0.276,0,0.5,0.224,0.5,0.5	S78.776,29,78.5,29z">
                                    </path>
                                </svg></a></td>
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
        const showDeletePopup = ref(false);
        const sorties = ref([]);
        const searchQuery = ref("");
        const sortColumn = ref("");
        const sortOrder = ref("asc");


        const sortieToDelete = ref(null);

        const columns = [
            { key: "produit.nom", label: "Nom du Produit" },
            { key: "produit.image_url", label: "Image" },
            { key: "quantité", label: "Quantité Sortie" },
            { key: "updated_at", label: "Date de Sortie" },
            { key: "number_after_reduce", label: "Stock Actuel" },
            { key: "zone.nom", label: "Zone Concernée" },
            { key: "zone.nom", label: "A effectuer la sortie" },
            { key: "observation", label: "Observation" },
            { key: "delete", label: "Supprimer cette sortie" }
        ];

        const columnscsv = [
            { key: "produit.nom", label: "Nom du Produit" },
            { key: "quantité", label: "Quantité Sortie" },
            { key: "updated_at", label: "Date de Sortie" },
            { key: "number_after_reduce", label: "Stock Actuel" },
            { key: "zone.nom", label: "Zone Concernée" },
            { key: "zone.nom", label: "A effectuer la sortie" },
            { key: "observation", label: "Observation" } // Nouvelle colonne Observation pour CSV
        ];

        const fetchData = () => {
            axios
                .get(`/api/sorties`)
                .then((response) => {
                    sorties.value = response.data;
                    console.log(sorties.value)
                })
                .catch((error) => {
                    console.error("Erreur lors de la récupération des sorties:", error);
                });
        };

        const confirmDelete = (sortie) => {
            sortieToDelete.value = sortie;
            showDeletePopup.value = true;
            console.log(showDeletePopup.value)
        };

        const deleteSortie = () => {
            if (sortieToDelete.value) {
                axios
                    .post(`/api/cancel_sortie/${sortieToDelete.value.id}/delete`, {}, {
                        _method: 'DELETE'
                    })
                    .then(() => {
                        sorties.value = sorties.value.filter(
                            (s) => s.id !== sortieToDelete.value.id
                        );
                        sortieToDelete.value = null;
                        showDeletePopup.value = false;
                    })
                    .catch((error) => {
                        console.error("Erreur lors de la suppression:", error);
                    });
            }
        };

        const cancelDelete = () => {
            sortieToDelete.value = null;
            showDeletePopup.value = false;
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
                sortie.zone.type === 'zone' ? sortie.zone.nom : 'Aucun',
                sortie.zone.type === 'personne' ? sortie.zone.nom : 'Aucun',
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
                            <th>A effectuer la sortie</th>
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
            baseUrl: import.meta.env.VITE_APP_URL,
            searchQuery,
            refreshData,
            exportToCSV,
            filteredAndSortedData,
            sortData,
            confirmDelete,
            columns,
            sortColumn,
            sortOrder,
            printTable,
            showDeletePopup,
            sortieToDelete,
            cancelDelete,
            deleteSortie,
            columnscsv,
            exportToCSV,

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
        display: none;
        /* Masquer les images lors de l'impression */
    }
}

.fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.bg-black {
    background-color: rgba(0, 0, 0, 0.6);
}
</style>

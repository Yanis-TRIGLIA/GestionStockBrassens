<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold text-center mb-6">Liste des Produits</h1>

        <!-- Filtres et recherche -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <input v-model="rechercheNom" type="text" placeholder="Rechercher par nom"
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <!-- Tri par catégorie -->
            <select v-model="triCategorie"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Trier par catégorie</option>
                <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
                    {{ categorie.nom }}
                </option>
            </select>

            <!-- Tri par quantité -->
            <select v-model="triQuantite"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Trier par quantité</option>
                <option value="asc">Quantité : Croissante</option>
                <option value="desc">Quantité : Décroissante</option>
            </select>
        </div>

        <!-- Affichage des produits sous forme de lignes -->
        <table class="w-full table-auto border-collapse border border-gray-300 shadow-md rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-6 py-4 text-left">Image</th>
                    <th class="border border-gray-300 px-6 py-4 text-left">Nom</th>
                    <th class="border border-gray-300 px-6 py-4 text-left">Quantité</th>
                    <th class="border border-gray-300 px-6 py-4 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="produit in produitsFiltres" :key="produit.id" class="hover:bg-gray-50">
                    <td class="border border-gray-300 px-6 py-4">
                        <img :src="`${baseUrl}/storage/${produit.image_url}`" alt="Image du produit"
                            class="w-24 h-24 object-cover rounded-lg mx-auto" />
                    </td>
                    <td class="border border-gray-300 px-6 py-4">{{ produit.nom }}</td>
                    <td class="border border-gray-300 px-6 py-4">
                        <span :class="produit.quantité > 2 ? 'text-green-500' : 'text-red-500'">
                            {{ produit.quantité }}
                        </span>
                    </td>
                    <td class="border border-gray-300 px-6 py-4">
                        <router-link :to="`/prod/${produit.id}`"
                            class="bg-slate-900 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Voir Détails
                        </router-link>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Suppression de la pagination -->
        <!--
        <div class="flex justify-center mt-6">
            <button v-for="page in pages" :key="page" @click="changerPage(page)"
                class="mx-1 px-3 py-1 border rounded-md" :class="{
                    'bg-blue-500 text-white': page === pageActuelle,
                    'bg-gray-200': page !== pageActuelle,
                }">
                {{ page }}
            </button>
        </div>
        -->
    </div>
</template>


<script>
import axios from "axios";

export default {
    name: "Produits",
    data() {
        return {
            produits: [],
            categories: [], // Liste des catégories
            rechercheNom: "",
            triQuantite: "",
            triCategorie: "", // Pour le tri par catégorie
            pageActuelle: 1,
            produitsParPage: 10,
            baseUrl: import.meta.env.VITE_APP_URL,
        };
    },
    computed: {
        produitsFiltres() {
            let filtres = this.produits;

            // Filtrage par nom
            if (this.rechercheNom) {
                filtres = filtres.filter((produit) =>
                    produit.nom.toLowerCase().includes(this.rechercheNom.toLowerCase())
                );
            }

            // Filtrage par catégorie
            if (this.triCategorie) {
                filtres = filtres.filter((produit) =>
                    produit.categories.some(categorie => categorie.id === parseInt(this.triCategorie))
                );
            }

            // Tri par quantité
            if (this.triQuantite === "asc") {
                filtres.sort((a, b) => a.quantité - b.quantité);
            } else if (this.triQuantite === "desc") {
                filtres.sort((a, b) => b.quantité - a.quantité);
            }

            return filtres.slice(
                (this.pageActuelle - 1) * this.produitsParPage,
                this.pageActuelle * this.produitsParPage
            );
        },
        pages() {
            return Math.ceil(this.produits.length / this.produitsParPage);
        },
    },
    methods: {
        changerPage(page) {
            this.pageActuelle = page;
        },
    },
    mounted() {
        // Récupérer les produits et les catégories
        axios
            .get("/api/produits")
            .then((response) => {
                this.produits = response.data;
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des produits:", error);
            });

        axios
            .get("/api/categorie")
            .then((response) => {
                this.categories = response.data;
                console.log(response.data);
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des catégories:", error);
            });
    },
};
</script>




<style scoped>
/* Amélioration de la table */
table th, table td {
    text-align: left;
    padding: 12px;
    font-size: 16px;
}

table th {
    background-color: #f9fafb;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f3f4f6;
}

table tr:hover {
    background-color: #e0e7ff;
}

/* Image du produit */
img {
    transition: transform 0.2s ease-in-out;
}
img:hover {
    transform: scale(1.1);
}
</style>

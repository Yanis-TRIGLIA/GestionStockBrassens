<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold text-center mb-6">Liste des Produits</h1>

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <input
                v-model="rechercheNom"
                type="text"
                placeholder="Rechercher par nom"
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />

            <!-- Tri par quantité -->
            <select
                v-model="triQuantite"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <option value="">Trier par quantité</option>
                <option value="asc">Quantité : Croissante</option>
                <option value="desc">Quantité : Décroissante</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="produit in produitsFiltres"
                :key="produit.id"
                class="relative flex w-full max-w-xs flex-col overflow-hidden rounded-lg border border-gray-100 bg-white shadow-md"
            >
                <a class="relative mx-3 mt-3 flex h-60 overflow-hidden rounded-xl" href="#">
                    <img
                        class="object-cover"
                        :src="produit.image_url"
                        alt="Image du produit"
                    />
                    <span v-if="produit.promotion" class="absolute top-0 left-0 m-2 rounded-full bg-black px-2 text-center text-sm font-medium text-white">
                        {{ produit.promotion }}% OFF
                    </span>
                </a>
                <div class="mt-4 px-5 pb-5">
                    <a href="#">
                        <h5 class="text-xl tracking-tight text-slate-900 text-center">
                            {{ produit.nom }}
                        </h5>
                    </a>
                    <div class="mt-2 mb-5 items-center flex">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48">
                            <path fill="#FF9800" d="M40,42H8c-1.1,0-2-0.9-2-2V8c0-1.1,0.9-2,2-2h32c1.1,0,2,0.9,2,2v32C42,41.1,41.1,42,40,42z"></path>
                            <path fill="#FFB74D" d="M21 6H27V13H21z"></path>
                        </svg>
                        <p v-if="produit.quantité > 2" class="text-green-500">{{ produit.quantité }}</p>
                        <p v-else class="text-red-500">{{ produit.quantité }}</p>
                    </div>
                    <router-link
                        :to="`/prod/${produit.id}`"
                        class="flex items-center justify-center rounded-md bg-slate-900 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
                    >
                        Accéder à {{ produit.nom }}
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6">
            <button
                v-for="page in pages"
                :key="page"
                @click="changerPage(page)"
                class="mx-1 px-3 py-1 border rounded-md"
                :class="{
                    'bg-blue-500 text-white': page === pageActuelle,
                    'bg-gray-200': page !== pageActuelle,
                }"
            >
                {{ page }}
            </button>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "Produits",
    data() {
        return {
            produits: [],
            rechercheNom: "",
            triQuantite: "",
            pageActuelle: 1,
            produitsParPage: 10,
        };
    },
    computed: {
        produitsFiltres() {
            let filtres = this.produits;

            if (this.rechercheNom) {
                filtres = filtres.filter((produit) =>
                    produit.nom.toLowerCase().includes(this.rechercheNom.toLowerCase())
                );
            }

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
        axios
            .get("/api/produits")
            .then((response) => {
                this.produits = response.data;
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des produits:", error);
            });
    },
};
</script>

<style scoped>
.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
}
.pagination button {
    padding: 5px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.pagination button.active {
    background-color: #007bff;
    color: white;
}
</style>

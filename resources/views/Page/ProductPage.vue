<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold text-center mb-6">📋 Liste des Produits</h1>

        <!-- Filtres et recherche -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <input v-model="rechercheNom" type="text" placeholder="Rechercher par nom"
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <select v-model="triCategorie"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Trier par catégorie</option>
                <option v-for="categorie in categories" :key="categorie.id" :value="categorie.id">
                    {{ categorie.nom }}
                </option>
            </select>

            <select v-model="triQuantite"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Trier par quantité</option>
                <option value="asc">Quantité : Croissante</option>
                <option value="desc">Quantité : Décroissante</option>
            </select>

            <input v-model.number="quantiteMax" type="number" placeholder="Max Quantité ou Moins"
                class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <!-- Tableau responsive -->
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-gray-300 shadow-md rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-3">Image</th>
                        <th class="border border-gray-300 px-4 py-3">Nom</th>
                        <th class="border border-gray-300 px-4 py-3">Quantité</th>
                        <th v-if="user" class="border border-gray-300 px-4 py-3">🛒 Panier</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="produit in produitsFiltres" :key="produit.id" class="hover:bg-gray-50 cursor-pointer"
                        @click="$router.push(`/prod/${produit.id}`)">

                        <td class="border border-gray-300 px-4 py-3">
                            <img :src="`${baseUrl}/${produit.image_url}`" alt="Image du produit"
                                class="w-20 h-20 object-cover rounded-lg mx-auto" />
                        </td>
                        <td class="border border-gray-300 px-4 py-3 font-semibold">{{ produit.nom }}</td>
                        <td class="border border-gray-300 px-4 py-3">
                            <span :class="produit.quantité > 2 ? 'text-green-500' : 'text-red-500'">
                                {{ produit.quantité }}
                            </span>
                        </td>
                        <td v-if="user " class="border border-gray-300 px-4 py-3 text-center" @click.stop>
                            <div class="flex items-center justify-center space-x-2">
                                <button @click="modifierQuantite(produit, -1)"
                                    class="bg-red-500 text-white px-2 py-1 rounded">-</button>
                                <input type="number" v-model="panier[produit.id]" min="1"
                                    class="w-12 text-center border border-gray-300 rounded-lg" />
                                <button @click="modifierQuantite(produit, 1)"
                                    class="bg-green-500 text-white px-2 py-1 rounded">+</button>
                            </div>

                            <!-- Afficher le texte si le produit est déjà dans le panier -->
                            <div v-if="panier_verif.includes(produit.id)">
                                
                                <span class="text-green-500 font-bold">Déjà dans le panier</span>
                                 
                            </div>
                           

                            <!-- Sinon, afficher le bouton "Ajouter au panier" -->
                            <div v-else>
                                <!-- On affiche l'id du produit" -->
                                <button @click="ajouterAuPanier(produit)"
                                    class="mt-2 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                    Ajouter au panier
                                </button>
                            </div>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';


export default {
    name: "Produits",
    data() {
        return {
            produits: [],
            categories: [],
            rechercheNom: "",
            triQuantite: "",
            quantiteMax: null,
            triCategorie: "",
            baseUrl: import.meta.env.VITE_APP_URL,
            user: localStorage.getItem("auth_token"),
            panier: {},
            panier_verif:[],
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

            if (this.triCategorie) {
                filtres = filtres.filter((produit) =>
                    produit.categories.some(categorie => categorie.id === parseInt(this.triCategorie))
                );
            }

            if (this.triQuantite === "asc") {
                filtres.sort((a, b) => a.quantité - b.quantité);
            } else if (this.triQuantite === "desc") {
                filtres.sort((a, b) => b.quantité - a.quantité);
            }

            if (this.quantiteMax !== null && this.quantiteMax !== "") {
                filtres = filtres.filter(produit => produit.quantité <= this.quantiteMax);
            }

            return filtres;
        }
    },
    methods: {
        modifierQuantite(produit, valeur) {
            let nouvelleQuantite = (this.panier[produit.id] || 1) + valeur;
            if (nouvelleQuantite < 1) nouvelleQuantite = 1;
            this.panier[produit.id] = nouvelleQuantite;
        },

        


        loadPanier() {
            const token = localStorage.getItem("auth_token");
            if (!token) return;

            axios.get("/api/panier", {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json"
                }
            })
                .then(response => {
                    this.panier_verif = response.data.produits.map(produit => produit.id);
                
                })
                .catch(error => {
                    console.error("Erreur chargement panier:", error);
                });
                

        },

        async ajouterAuPanier(produit) {
            try {
                const token = localStorage.getItem("auth_token");

                if (!token) {
                    console.error("Aucun token trouvé, l'utilisateur n'est pas authentifié.");
                    return;
                }

                await axios.post("/api/panier/ajouter",
                    {
                        produit_id: produit.id,
                        quantite: this.panier[produit.id] || 1
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                            Accept: "application/json"
                        },
                        withCredentials: true
                    }
                );

                this.showSuccessToast("Produit ajouté au panier avec succès !");

            } catch (error) {
                console.error("Erreur ajout au panier:", error);
            }
        },

        showSuccessToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#4CAF50",
                stopOnFocus: true
            }).showToast();
            //on reload
            setTimeout(() => {
                location.reload();
            }, 1500);
        },
        showErrorToast(message) {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                backgroundColor: "#FF0000",
                stopOnFocus: true
            }).showToast();
        }

    },
    mounted() {
        axios.get("/api/produits")
            .then((response) => { this.produits = response.data; })
            .catch((error) => { console.error("Erreur produits:", error); });

        axios.get("/api/categorie")
            .then((response) => { this.categories = response.data; })
            .catch((error) => { console.error("Erreur catégories:", error); });

        this.loadPanier();

    }
};
</script>

<style scoped>
/* Table styling */
table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 12px;
    text-align: center;
}

th {
    background-color: #f9fafb;
    font-weight: bold;
}

tr:hover {
    background-color: #e0e7ff;
}

/* Responsive */
@media (max-width: 768px) {
    table {
        font-size: 14px;
    }

    img {
        width: 50px;
        height: 50px;
    }
}
</style>

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
                    console.log("Produit récupéré :", response.data);
                    this.produit = response.data; // Vérifiez ici si "categories" existe
                })
                .catch(() => {
                    this.showErrorToast("Erreur lors du chargement du produit.");
                });
        },

        updateProduit(formData) {
            const id = this.$route.params.id;
            console.log("Updating product with ID:", id);

            // Vérifier si file_product est vide et, dans ce cas, l'exclure
            if (typeof formData.get("file_product") == "string") {
                console.log("file_product is empty or not a string, it will be excluded from the request");
                formData.delete("file_product");
            }

            // Envoyer le FormData directement
            axios
                .post(`/api/produits/${id}?_method=POST`, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data", // Spécifier multipart/form-data pour le traitement des fichiers
                    },
                })
                .then((response) => {
                    console.log("Produit mis à jour :", response.data);
                    this.produit = response.data;
                    this.showSuccessToast("Produit modifié avec succès !");
                    this.$router.push("/admin/produits");
                })
                .catch((error) => {
                    // Détail des erreurs pour affichage utilisateur
                    console.error("Erreur lors de la modification du produit :", error.response || error);

                    // Axios error response parsing
                    const resp = error && error.response ? error.response : null;
                    if (resp && resp.status === 422 && resp.data) {
                        // Validation errors from Laravel
                        const data = resp.data;
                        // Laravel returns { message: '...', errors: { field: ["...", ...] } }
                        let messages = [];
                        if (data.errors && typeof data.errors === 'object') {
                            for (const key in data.errors) {
                                if (Array.isArray(data.errors[key])) {
                                    messages.push(...data.errors[key]);
                                } else if (typeof data.errors[key] === 'string') {
                                    messages.push(data.errors[key]);
                                }
                            }
                        }
                        // If no structured errors, use message
                        if (messages.length === 0 && data.message) messages.push(data.message);

                        // Traduire les messages anglais en français quand c'est possible
                        messages = messages.map((m) => this.translateMessage(m));

                        // Show each validation error as a toast (or one combined)
                        if (messages.length === 1) this.showErrorToast(messages[0]);
                        else if (messages.length > 1) this.showErrorToast(messages.join(' \n'));
                        else this.showErrorToast('Erreur de validation inconnue.');
                    } else if (resp && resp.data && resp.data.message) {
                        // Generic server message (traduire si anglais)
                        this.showErrorToast(this.translateMessage(resp.data.message));
                    } else if (error && error.message) {
                        this.showErrorToast(this.translateMessage(error.message));
                    } else {
                        this.showErrorToast('Erreur lors de la modification du produit.');
                    }
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

        // Traduction basique des messages d'erreur anglais -> français
        translateMessage(message) {
            if (!message || typeof message !== 'string') return message;

            // Map des noms de champs techniques -> libellés français
            const fieldMap = {
                'nom': 'Nom',
                'name': 'Nom',
                'description': 'Description',
                'prix': 'Prix',
                'price': 'Prix',
                'reference': 'Référence',
                'quantite': 'Quantité',
                'quantité': 'Quantité',
                'categories': 'catégories',
                'category': 'catégorie',
                'image': 'image',
                'file_product': 'fichier produit',
                'fiches_techniques': 'fiches techniques',
                'email': 'e-mail',
            };

            // Patterns courants Laravel (validation) avec template de sortie
            const patterns = [
                {re: /The (.+) field is required\./i, tpl: "Le champ {field} est requis."},
                {re: /The (.+) must be a number\./i, tpl: "Le champ {field} doit être un nombre."},
                {re: /The (.+) must be an integer\./i, tpl: "Le champ {field} doit être un entier."},
                {re: /The (.+) must be a valid email address\./i, tpl: "Le champ {field} doit être une adresse e-mail valide."},
                {re: /The (.+) must be at least (\d+) characters\./i, tpl: "Le champ {field} doit contenir au moins $2 caractères."},
                {re: /The (.+) may not be greater than (\d+) characters\./i, tpl: "Le champ {field} ne doit pas dépasser $2 caractères."},
                {re: /The (.+) must be at most (\d+) characters\./i, tpl: "Le champ {field} doit contenir au maximum $2 caractères."},
                {re: /The (.+) has already been taken\./i, tpl: "Le champ {field} est déjà utilisé."},
                {re: /The (.+) must be a string\./i, tpl: "Le champ {field} doit être une chaîne de caractères."},
                {re: /The (.+) must be an array\./i, tpl: "Le champ {field} doit être un tableau."},
                {re: /The (.+) must be a file\./i, tpl: "Le champ {field} doit être un fichier."},
                {re: /Unprocessable Content/i, tpl: "Contenu invalide."},
            ];

            for (const p of patterns) {
                const match = p.re.exec(message);
                if (match) {
                    // match[1] = field name (ex: "Nom" or "nom" or "categories")
                    const rawField = match[1] ? String(match[1]).toLowerCase() : null;
                    const fieldLabel = rawField && fieldMap[rawField] ? fieldMap[rawField] : (rawField ? rawField.charAt(0).toUpperCase() + rawField.slice(1) : 'Champ');

                    // Construire le message à partir du template
                    let out = p.tpl.replace('{field}', fieldLabel);
                    // Remplacer les captures supplémentaires comme $2, $3
                    for (let i = 2; i < match.length; i++) {
                        out = out.replace('$' + i, match[i]);
                    }
                    return out;
                }
            }

            // Traductions exactes rapides
            const map = {
                'The categories field is required.': "Le champ catégories est requis.",
                'The image field is required.': "L'image est requise.",
                'Unprocessable Content': "Contenu invalide.",
            };
            if (map[message]) return map[message];

            return message; // fallback: renvoyer tel quel
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

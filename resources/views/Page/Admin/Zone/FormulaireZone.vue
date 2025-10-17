<template>
    <div>
        <form @submit.prevent="submitForm" class="max-w-3xl mx-auto">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <h2 class="text-xl font-semibold mb-4">Informations sur {{ formData.nom }}</h2>

                        <div class="mb-4">
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input v-model="formData.nom" type="text" id="nom"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Nom" />
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium mb-2">Image</h3>
                        <div @dragover.prevent="onDragOver" @dragenter.prevent="onDragEnter"
                            @dragleave.prevent="onDragLeave" @drop.prevent="onDropImage"
                            :class="['border-dashed border-2 rounded-md p-4 text-center bg-white', isDragOver ? 'border-indigo-400 bg-indigo-50' : 'border-indigo-200']">
                            <div class="flex items-center justify-center gap-3 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                    <path d="M7 10l5-5 5 5" />
                                    <path d="M12 5v12" />
                                </svg>
                                <p class="text-sm text-gray-600">Glissez-déposez une image ici ou</p>
                            </div>
                            <div class="mt-3">
                                <button type="button" @click="triggerImageInput"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700">Choisir
                                    une image</button>
                                <input ref="imageInput" type="file" id="image" @change="handleImageChange"
                                    class="hidden" accept="image/*" />
                            </div>

                            <div v-if="imagePreview" class="mt-4">
                                <img :src="fullImageUrl" alt="Aperçu"
                                    class="w-full h-40 object-cover rounded-md shadow-sm" />
                                <div class="flex justify-between mt-2">
                                    <button type="button" @click="removeImage"
                                        class="px-3 py-2 bg-red-500 text-white rounded-md text-sm">Retirer</button>
                                    <button type="button" @click="openInNewTab"
                                        class="px-3 py-2 bg-gray-100 rounded-md text-sm">Ouvrir</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" @click="goBack" class="px-4 py-2 rounded-md bg-gray-100">Annuler</button>
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Enregistrer</button>
                </div>
            </div>
        </form>

        <div v-if="showCropModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="closeCropModal">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Recadrer l'image</h3>
                </div>

                <div class="flex-1 overflow-auto p-4">
                    <div class="max-h-[60vh]">
                        <img ref="cropperImage" :src="tempImageSrc" class="max-w-full" />
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 flex justify-end gap-3">
                    <button type="button" @click="closeCropModal"
                        class="px-4 py-2 rounded-md bg-gray-100 hover:bg-gray-200">Annuler</button>
                    <button type="button" @click="applyCrop"
                        class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Valider</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "FormulaireZone",
    props: {
        zone: {
            type: Object,
            required: false,
            default: () => ({
                nom: "",
                type: "zone",
                image_url: null,
            }),
        },
        // 💡 NOUVELLE PROP : Aspect ratio pour le rognage. NaN pour un rognage libre.
        // C'est ce qui assurera le même comportement pour l'agent si vous ne le forcez pas.
        cropAspectRatio: {
            type: Number,
            required: false,
            default: NaN, 
        },
    },

    data() {
        return {
            formData: {
                nom: this.zone?.nom || "",
                type: this.zone?.type || "zone",
                image: null,
            },
            baseUrl: import.meta.env.VITE_APP_URL,
            imagePreview: null,
            isDragOver: false,
            dragCounter: 0,
            showCropModal: false,
            tempImageSrc: null,
            cropper: null,
            Cropper: null,
        };
    },

    computed: {
        fullImageUrl() {
            if (!this.imagePreview) return null;
            
            // Si c'est un data URL (blob) ou une URL complète, retourner tel quel
            if (this.imagePreview.startsWith('data:') || 
                this.imagePreview.startsWith('http://') || 
                this.imagePreview.startsWith('https://')) {
                return this.imagePreview;
            }
            
            // Sinon, c'est un chemin relatif, ajouter baseUrl
            return this.baseUrl + '/' + this.imagePreview;
        }
    },

    watch: {
        zone: {
            immediate: true,
            handler(newZone) {
                if (newZone) {
                    this.formData.nom = newZone.nom || "";
                    this.formData.type = newZone.type || "zone";

                    // Prévisualiser l'image existante si elle existe
                    if (newZone.image_url && !this.imagePreview) {
                        this.imagePreview = newZone.image_url;
                    }
                }
            }
        },
    },

    mounted() {
        // Charger l'image existante au montage du composant
        if (this.zone?.image_url && !this.imagePreview) {
            this.imagePreview = this.zone.image_url;
        }
    },

    methods: {

        goBack() {
            if (this.$router && this.$router.back) this.$router.back();
            else window.history.back();
        },

        async loadCropper() {
            if (!window.Cropper) {
                // Charger le CSS de Cropper.js
                if (!document.getElementById('cropperjs-css')) {
                    const link = document.createElement('link');
                    link.id = 'cropperjs-css';
                    link.rel = 'stylesheet';
                    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css';
                    document.head.appendChild(link);
                }

                // Charger le script de Cropper.js
                if (!document.getElementById('cropperjs-script')) {
                    return new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.id = 'cropperjs-script';
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js';
                        script.onload = () => {
                            this.Cropper = window.Cropper;
                            resolve();
                        };
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });
                }
            } else {
                this.Cropper = window.Cropper;
            }
        },

        async handleImageChange(event) {
            const file = event.target.files[0];
            if (file) {
                await this.processImageFile(file);
            }
        },

        async onDropImage(event) {
            this.dragCounter = 0;
            this.isDragOver = false;
            let file = null;
            try {
                const dt = event.dataTransfer;
                if (dt && dt.items && dt.items.length) {
                    for (let i = 0; i < dt.items.length; i++) {
                        const it = dt.items[i];
                        if (it.kind === 'file') {
                            file = it.getAsFile();
                            break;
                        }
                    }
                }
                if (!file && dt && dt.files && dt.files.length) {
                    file = dt.files[0];
                }
            } catch (e) {
                console.warn('drop parsing failed, falling back', e);
            }
            if (file) {
                await this.processImageFile(file);
            }
        },

        async processImageFile(file) {
            await this.loadCropper();

            const reader = new FileReader();
            reader.onload = async (e) => {
                this.tempImageSrc = e.target.result;
                this.showCropModal = true;
                await this.$nextTick();
                // Attendre un peu plus pour s'assurer que le DOM est prêt
                setTimeout(() => {
                    this.initCropper();
                }, 100);
            };
            reader.readAsDataURL(file);
        },

        initCropper() {
            if (this.$refs.cropperImage && (this.Cropper || window.Cropper)) {
                const img = this.$refs.cropperImage;
                const CropperClass = this.Cropper || window.Cropper;

                const initializeCropper = () => {
                    if (this.cropper) {
                        this.cropper.destroy();
                    }

                    this.cropperReady = false;

                    try {
                        this.cropper = new CropperClass(img, {
                            // 🚀 MODIFICATION CLÉ ICI : Utilisation de la nouvelle prop
                            aspectRatio: this.cropAspectRatio, 
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.8,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: true,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                        });

                        // Attendre un court instant pour s'assurer que tout est initialisé
                        setTimeout(() => {
                            this.cropperReady = true;
                            console.log('Cropper is ready', this.cropper);
                        }, 300);
                    } catch (error) {
                        console.error('Error initializing cropper:', error);
                    }
                };

                // Si l'image est déjà chargée
                if (img.complete && img.naturalHeight !== 0) {
                    initializeCropper();
                } else {
                    // Sinon attendre le chargement
                    img.onload = initializeCropper;
                }
            }
        },

        applyCrop() {
            console.log('applyCrop called, cropperReady:', this.cropperReady, 'cropper:', this.cropper);

            if (!this.cropper) {
                console.error('Cropper instance not found');
                return;
            }

            try {
                const canvas = this.cropper.getCroppedCanvas();

                if (!canvas) {
                    console.error('Failed to get cropped canvas');
                    return;
                }

                canvas.toBlob((blob) => {
                    if (!blob) {
                        console.error('Failed to create blob');
                        return;
                    }

                    const file = new File([blob], 'cropped-image.jpg', { type: 'image/jpeg' });
                    this.formData.image = file;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    this.closeCropModal();
                }, 'image/jpeg', 0.95);
            } catch (error) {
                console.error('Error during crop:', error);
            }
        },

        closeCropModal() {
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
            this.cropperReady = false;
            this.showCropModal = false;
            this.tempImageSrc = null;
        },

        onDragOver(e) {
            if (e.dataTransfer) e.dataTransfer.dropEffect = 'copy';
            this.isDragOver = true;
            e.preventDefault();
        },

        onDragEnter(e) {
            this.dragCounter = (this.dragCounter || 0) + 1;
            this.isDragOver = true;
            e.preventDefault();
        },

        onDragLeave(e) {
            this.dragCounter = Math.max(0, (this.dragCounter || 0) - 1);
            if (this.dragCounter === 0) this.isDragOver = false;
            e.preventDefault();
        },

        triggerImageInput() {
            if (this.$refs.imageInput) this.$refs.imageInput.click();
        },

        removeImage() {
            this.formData.image = null;
            this.imagePreview = null;
        },

        openInNewTab() {
            if (!this.fullImageUrl) return;
            window.open(this.fullImageUrl, '_blank');
        },

        submitForm() {
            const formData = new FormData();
            formData.append("nom", this.formData.nom);
            console.log(this.formData);
            if (this.formData.image) {
                formData.append("image", this.formData.image);
            }
            if (this.formData.type) {
                formData.append("type", this.formData.type);
            }
            this.$emit("submit", formData);
        },
    },

    beforeUnmount() {
        if (this.cropper) {
            this.cropper.destroy();
        }
    },
};
</script>
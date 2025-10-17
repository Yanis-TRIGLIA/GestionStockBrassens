<template>
    <div>
        <form @submit.prevent="submitForm" class="max-w-3xl mx-auto">
            <div class="bg-white shadow-lg rounded-lg p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Infos utilisateur -->
                    <div class="lg:col-span-2">
                        <h2 class="text-xl font-semibold mb-4">Informations utilisateur</h2>
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input v-model="formData.name" type="text" id="name"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Nom de l'utilisateur" />
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input v-model="formData.email" type="email" id="email"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Email de l'utilisateur" />
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de
                                passe</label>
                            <input v-model="formData.password" type="password" id="password"
                                class="block w-full rounded-md border border-gray-200 px-3 py-2 focus:ring-2 focus:ring-indigo-300"
                                placeholder="Mot de passe" />
                        </div>
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" id="is_admin" v-model="formData.is_admin"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded" />
                            <label for="is_admin" class="ml-2 text-sm text-gray-700">Est-ce un administrateur ?</label>
                        </div>
                    </div>

                    <!-- Photo de profil -->
                    <div>
                        <h3 class="text-lg font-medium mb-2">Photo de profil</h3>
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
                                    une image
                                </button>
                                <input ref="imageInput" type="file" id="image" @change="handleImageChange"
                                    class="hidden" accept="image/*" />
                            </div>

                            <div v-if="imagePreview" class="mt-4">
                                <img :src="fullImageUrl" alt="Aperçu"
                                    class="w-32 h-32 mx-auto object-cover rounded-full shadow-sm border-2 border-gray-200" />
                                <div class="flex justify-center gap-2 mt-3">
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
                    <button type="button" @click="goBack"
                        class="px-4 py-2 rounded-md bg-gray-100">Annuler</button>
                    <button type="submit"
                        class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">Enregistrer</button>
                </div>
            </div>
        </form>

        <!-- Modal de crop (uniquement pour PNG/JPG) -->
        <div v-if="showCropModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="closeCropModal">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] flex flex-col">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold">Recadrer l'image de profil</h3>
                </div>
                <div class="flex-1 overflow-auto p-4">
                    <div class="max-h-[60vh] flex justify-center">
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
    name: "FormulaireUser",
    props: {
        user: { type: Object, default: () => ({}) },
        isEditMode: { type: Boolean, default: false },
    },
    data() {
        return {
            formData: {
                name: "",
                email: "",
                password: "",
                is_admin: false,
                photo_profil: null,
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
            if (this.imagePreview.startsWith("data:") ||
                this.imagePreview.startsWith("http://") ||
                this.imagePreview.startsWith("https://")) {
                return this.imagePreview;
            }
            return this.baseUrl + "/" + this.imagePreview;
        },
    },
    watch: {
        user: {
            immediate: true,
            handler(newUser) {
                if (newUser) {
                    this.formData = {
                        name: newUser.name || "",
                        email: newUser.email || "",
                        password: "",
                        is_admin: newUser.is_admin || false,
                        photo_profil: null,
                    };
                    if (newUser.photo_profil && !this.imagePreview) {
                        this.imagePreview = newUser.photo_profil;
                    }
                }
            },
        },
    },
    mounted() {
        if (this.user?.photo_profil && !this.imagePreview) {
            this.imagePreview = this.user.photo_profil;
        }
    },
    methods: {
        async loadCropper() {
            if (!window.Cropper) {
                if (!document.getElementById("cropperjs-css")) {
                    const link = document.createElement("link");
                    link.id = "cropperjs-css";
                    link.rel = "stylesheet";
                    link.href = "https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css";
                    document.head.appendChild(link);
                }
                if (!document.getElementById("cropperjs-script")) {
                    return new Promise((resolve, reject) => {
                        const script = document.createElement("script");
                        script.id = "cropperjs-script";
                        script.src = "https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js";
                        script.onload = () => { this.Cropper = window.Cropper; resolve(); };
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });
                }
            } else {
                this.Cropper = window.Cropper;
            }
        },

        goBack() {
            if (this.$router && this.$router.back) this.$router.back();
            else window.history.back();
        },

        async handleImageChange(event) {
            const file = event.target.files[0];
            if (file) await this.processImageFile(file);
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
                        if (it.kind === "file") { file = it.getAsFile(); break; }
                    }
                }
                if (!file && dt && dt.files && dt.files.length) file = dt.files[0];
            } catch (e) { console.warn("drop parsing failed", e); }
            if (file) await this.processImageFile(file);
        },

        async processImageFile(file) {
            const isGif = file.type === "image/gif";

            if (isGif) {
                // GIF animé : on ne crop pas
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                    this.formData.photo_profil = file;
                };
                reader.readAsDataURL(file);
            } else {
                // PNG/JPG : crop normal
                await this.loadCropper();
                const reader = new FileReader();
                reader.onload = async (e) => {
                    this.tempImageSrc = e.target.result;
                    this.showCropModal = true;
                    await this.$nextTick();
                    setTimeout(() => { this.initCropper(); }, 100);
                };
                reader.readAsDataURL(file);
            }
        },

        initCropper() {
            if (!this.$refs.cropperImage || !(this.Cropper || window.Cropper)) return;
            const img = this.$refs.cropperImage;
            const CropperClass = this.Cropper || window.Cropper;
            if (this.cropper) this.cropper.destroy();

            this.cropper = new CropperClass(img, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: "move",
                autoCropArea: 0.8,
                restore: false,
                guides: true,
                center: true,
                highlight: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        },

        applyCrop() {
            if (!this.cropper) return;
            const canvas = this.cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: "high" });
            canvas.toBlob((blob) => {
                const file = new File([blob], "profile-picture.jpg", { type: "image/jpeg" });
                this.formData.photo_profil = file;

                const reader = new FileReader();
                reader.onload = (e) => { this.imagePreview = e.target.result; };
                reader.readAsDataURL(file);

                this.closeCropModal();
            }, "image/jpeg", 0.95);
        },

        closeCropModal() {
            if (this.cropper) { this.cropper.destroy(); this.cropper = null; }
            this.showCropModal = false;
            this.tempImageSrc = null;
        },

        onDragOver(e) { if (e.dataTransfer) e.dataTransfer.dropEffect = "copy"; this.isDragOver = true; e.preventDefault(); },
        onDragEnter(e) { this.dragCounter++; this.isDragOver = true; e.preventDefault(); },
        onDragLeave(e) { this.dragCounter--; if (this.dragCounter <= 0) this.isDragOver = false; e.preventDefault(); },
        triggerImageInput() { this.$refs.imageInput?.click(); },
        removeImage() { this.formData.photo_profil = null; this.imagePreview = null; },
        openInNewTab() { if (!this.fullImageUrl) return; window.open(this.fullImageUrl, "_blank"); },

        submitForm() {
            const formData = new FormData();
            formData.append("name", this.formData.name);
            formData.append("email", this.formData.email);
            if (this.formData.password) formData.append("password", this.formData.password);
            formData.append("is_admin", this.formData.is_admin ?? false);
            if (this.formData.photo_profil) formData.append("photo_profil", this.formData.photo_profil);

            console.log("Données envoyées :", this.formData);
            this.$emit("submit", formData);
        },
    },

    beforeUnmount() {
        if (this.cropper) this.cropper.destroy();
    },
};
</script>

<style scoped>
::v-deep .cropper-view-box,
::v-deep .cropper-face {
    border-radius: 50%;
}
</style>

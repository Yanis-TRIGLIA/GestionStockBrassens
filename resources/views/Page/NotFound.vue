<template>
    <div class="bg-gray-900 min-h-screen flex items-center justify-center p-4 overflow-hidden">
      <!-- Stars Background -->
      <div id="stars" class="fixed inset-0"></div>
  
      <!-- Main Content -->
      <div class="error-container relative z-10 text-center">
        <div class="relative space-animation mb-8">
          <svg class="w-32 h-32 mx-auto" viewBox="0 0 100 100">
            <ellipse cx="50" cy="40" rx="30" ry="10" fill="#4F46E5"/>
            <circle cx="50" cy="35" r="20" fill="#818CF8"/>
            <ellipse cx="50" cy="30" rx="10" ry="5" fill="#C7D2FE"/>
            <path class="ufo-beam" d="M40 40 L30 80 L70 80 L60 40" fill="rgba(79, 70, 229, 0.2)"/>
          </svg>
        </div>
        <h1 class="text-8xl font-bold text-white mb-4 space-animation">4<span class="inline-block portal">0</span>4</h1>
        <p class="text-xl text-blue-200 mb-8">Je crois que vous êtes perdus !</p>
  
        <div class="space-y-4 space-x-4">
          <router-link to="/" class="px-6 py-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transform hover:scale-105 transition">
            Retourner à l'accueil
          </router-link>
          <button @click="$router.go(-1)" class="px-6 py-3 bg-purple-600 text-white rounded-full hover:bg-purple-700 transform hover:scale-105 transition">
            Revenir en arrière
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "NotFound",
  };
  </script>
  
  <style scoped>
  /* Ajoute ici tes animations et styles */
  </style>
  
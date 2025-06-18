<template>
  <div id="app">
    <router-view />
    <NotificationToast />
  </div>
</template>

<script setup lang="ts">
import NotificationToast from '@/components/NotificationToast.vue';
</script>

<style scoped>
#app {
  min-height: 100vh;
}
</style>
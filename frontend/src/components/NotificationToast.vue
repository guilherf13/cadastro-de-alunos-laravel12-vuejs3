<template>
  <TransitionGroup
    name="notification"
    tag="div"
    class="fixed top-8 left-1/2 transform -translate-x-1/2 z-50 space-y-4 w-full max-w-sm px-4"
  >
    <div
      v-for="notification in notifications"
      :key="notification.id"
      :class="[
        'relative overflow-hidden rounded-xl shadow-2xl backdrop-blur-sm border',
        getNotificationStyles(notification.type)
      ]"
      class="w-full transform transition-all duration-300 ease-in-out"
    >
      <!-- Barra lateral colorida -->
      <div :class="getSidebarColor(notification.type)" class="absolute left-0 top-0 w-1 h-full"></div>
      
      <!-- Conteúdo principal -->
      <div class="bg-white/95 backdrop-blur-sm p-6 pl-8">
        <div class="flex items-start gap-4">
          <!-- Ícone -->
          <div :class="getIconBgColor(notification.type)" class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
            <component :is="getIcon(notification.type)" :class="getIconColor(notification.type)" class="w-5 h-5" />
          </div>
          
          <!-- Texto -->
          <div class="flex-1 min-w-0">
            <h4 class="text-lg font-semibold text-gray-900 mb-1">
              {{ notification.title }}
            </h4>
            <p v-if="notification.message" class="text-sm text-gray-600 leading-relaxed break-words">
              {{ notification.message }}
            </p>
          </div>
          
          <!-- Botão fechar -->
          <button
            @click="notificationsStore.removeNotification(notification.id)"
            class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors duration-200"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>
      </div>
      
      <!-- Barra de progresso (auto-close) -->
      <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-200/50">
        <div 
          :class="getSidebarColor(notification.type)"
          class="h-full transition-all duration-75 ease-linear notification-progress"
        ></div>
      </div>
    </div>
  </TransitionGroup>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { 
  CheckCircleIcon, 
  ExclamationTriangleIcon, 
  XCircleIcon, 
  InformationCircleIcon,
  XMarkIcon 
} from '@heroicons/vue/24/outline';
import { useNotificationsStore } from '@/stores/notifications';
import type { Notification } from '@/stores/notifications';

const notificationsStore = useNotificationsStore();
const notifications = computed(() => notificationsStore.notifications);

function getIcon(type: Notification['type']) {
  const icons = {
    success: CheckCircleIcon,
    error: XCircleIcon,
    warning: ExclamationTriangleIcon,
    info: InformationCircleIcon
  };
  return icons[type];
}

function getNotificationStyles(type: Notification['type']) {
  const styles = {
    success: 'border-green-200/50 shadow-green-100/20',
    error: 'border-red-200/50 shadow-red-100/20',
    warning: 'border-yellow-200/50 shadow-yellow-100/20',
    info: 'border-blue-200/50 shadow-blue-100/20'
  };
  return styles[type];
}

function getSidebarColor(type: Notification['type']) {
  const colors = {
    success: 'bg-gradient-to-b from-green-400 to-green-500',
    error: 'bg-gradient-to-b from-red-400 to-red-500',
    warning: 'bg-gradient-to-b from-yellow-400 to-yellow-500',
    info: 'bg-gradient-to-b from-blue-400 to-blue-500'
  };
  return colors[type];
}

function getIconBgColor(type: Notification['type']) {
  const colors = {
    success: 'bg-green-50 border border-green-200',
    error: 'bg-red-50 border border-red-200',
    warning: 'bg-yellow-50 border border-yellow-200',
    info: 'bg-blue-50 border border-blue-200'
  };
  return colors[type];
}

function getIconColor(type: Notification['type']) {
  const colors = {
    success: 'text-green-600',
    error: 'text-red-600',
    warning: 'text-yellow-600',
    info: 'text-blue-600'
  };
  return colors[type];
}
</script>

<style scoped>
.notification-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.notification-leave-active {
  transition: all 0.3s ease-in;
}

.notification-enter-from {
  opacity: 0;
  transform: translateY(-100%) scale(0.9);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}

.notification-move {
  transition: transform 0.3s ease;
}

/* Efeito de hover sutil */
.notification-item:hover {
  transform: translateY(-1px);
}

/* Animação da barra de progresso */
@keyframes progress {
  from {
    width: 100%;
  }
  to {
    width: 0%;
  }
}

.notification-progress {
  animation: progress 5s linear forwards;
}
</style>
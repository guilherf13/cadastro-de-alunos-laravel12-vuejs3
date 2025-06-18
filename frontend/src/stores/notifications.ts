import { defineStore } from 'pinia';
import { ref } from 'vue';

export interface Notification {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title: string;
  message?: string;
  duration?: number;
}

export const useNotificationsStore = defineStore('notifications', () => {
  const notifications = ref<Notification[]>([]);

  function addNotification(notification: Omit<Notification, 'id'>) {
    const id = Date.now().toString();
    const newNotification: Notification = {
      id,
      duration: 5000,
      ...notification
    };
    
    notifications.value.push(newNotification);

    if (newNotification.duration) {
      setTimeout(() => {
        removeNotification(id);
      }, newNotification.duration);
    }

    return id;
  }

  function removeNotification(id: string) {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index > -1) {
      notifications.value.splice(index, 1);
    }
  }

  function success(title: string, message?: string) {
    return addNotification({ type: 'success', title, message });
  }

  function error(title: string, message?: string) {
    return addNotification({ type: 'error', title, message, duration: 8000 });
  }

  function warning(title: string, message?: string) {
    return addNotification({ type: 'warning', title, message });
  }

  function info(title: string, message?: string) {
    return addNotification({ type: 'info', title, message });
  }

  return {
    notifications,
    addNotification,
    removeNotification,
    success,
    error,
    warning,
    info
  };
});
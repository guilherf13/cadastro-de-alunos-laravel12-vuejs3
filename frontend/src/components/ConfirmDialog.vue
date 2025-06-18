<template>
  <TransitionRoot as="template" :show="show">
    <Dialog as="div" class="relative z-10" @close="$emit('close')">
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
      </TransitionChild>

      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild
            as="template"
            enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100"
            leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
              <div class="sm:flex sm:items-start">
                <div :class="iconBgClasses" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10">
                  <component :is="iconComponent" :class="iconClasses" class="h-6 w-6" />
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">
                    {{ title }}
                  </DialogTitle>
                  <div class="mt-2">
                    <p class="text-sm text-gray-500">{{ message }}</p>
                  </div>
                </div>
              </div>
              <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <BaseButton
                  :variant="confirmVariant"
                  :loading="loading"
                  @click="$emit('confirm')"
                  class="w-full sm:ml-3 sm:w-auto"
                >
                  {{ confirmText }}
                </BaseButton>
                <BaseButton
                  variant="outline"
                  @click="$emit('close')"
                  class="mt-3 w-full sm:mt-0 sm:w-auto"
                  :disabled="loading"
                >
                  {{ cancelText }}
                </BaseButton>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from '@headlessui/vue';
import { ExclamationTriangleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';
import BaseButton from './BaseButton.vue';

interface Props {
  show: boolean;
  title: string;
  message: string;
  type?: 'danger' | 'warning' | 'info';
  confirmText?: string;
  cancelText?: string;
  loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'danger',
  confirmText: 'Confirmar',
  cancelText: 'Cancelar',
  loading: false
});

defineEmits<{
  close: [];
  confirm: [];
}>();

const iconComponent = computed(() => {
  const icons = {
    danger: ExclamationTriangleIcon,
    warning: ExclamationTriangleIcon,
    info: InformationCircleIcon
  };
  return icons[props.type];
});

const iconBgClasses = computed(() => {
  const classes = {
    danger: 'bg-error-100',
    warning: 'bg-warning-100',
    info: 'bg-primary-100'
  };
  return classes[props.type];
});

const iconClasses = computed(() => {
  const classes = {
    danger: 'text-error-600',
    warning: 'text-warning-600',
    info: 'text-primary-600'
  };
  return classes[props.type];
});

const confirmVariant = computed(() => {
  const variants = {
    danger: 'danger' as const,
    warning: 'warning' as const,
    info: 'primary' as const
  };
  return variants[props.type];
});
</script>
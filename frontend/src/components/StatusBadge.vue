<template>
  <span
    :class="badgeClasses"
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
  >
    <span :class="dotClasses" class="w-2 h-2 rounded-full mr-1.5"></span>
    {{ status }}
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

type Status = 'Pendente' | 'Aprovado' | 'Cancelado';

interface Props {
  status: Status;
}

const props = defineProps<Props>();

const badgeClasses = computed(() => {
  switch (props.status) {
    case 'Aprovado':
      return 'bg-success-50 text-success-700';
    case 'Pendente':
      return 'bg-warning-50 text-warning-700';
    case 'Cancelado':
      return 'bg-error-50 text-error-700';
    default:
      return 'bg-gray-50 text-gray-700';
  }
});

const dotClasses = computed(() => {
  switch (props.status) {
    case 'Aprovado':
      return 'bg-success-400';
    case 'Pendente':
      return 'bg-warning-400';
    case 'Cancelado':
      return 'bg-error-400';
    default:
      return 'bg-gray-400';
  }
});
</script>
<template>
  <div class="space-y-1">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700">
      {{ label }}
      <span v-if="required" class="text-error-500">*</span>
    </label>
    
    <select
      :id="id"
      :value="modelValue"
      :required="required"
      :disabled="disabled"
      :class="selectClasses"
      @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
      <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
      <slot></slot>
    </select>
    
    <p v-if="error" class="text-sm text-error-600">{{ error }}</p>
    <p v-else-if="helper" class="text-sm text-gray-500">{{ helper }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  id?: string;
  label?: string;
  modelValue?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  error?: string;
  helper?: string;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  required: false,
  disabled: false
});

defineEmits<{
  'update:modelValue': [value: string];
}>();

const selectClasses = computed(() => {
  const base = 'block w-full rounded-lg border px-3 py-2.5 text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors sm:text-sm disabled:bg-gray-50 disabled:cursor-not-allowed';
  
  if (props.error) {
    return `${base} border-error-300 focus:border-error-500 focus:ring-error-500`;
  }
  
  return `${base} border-gray-300 focus:border-primary-500 focus:ring-primary-500`;
});
</script>
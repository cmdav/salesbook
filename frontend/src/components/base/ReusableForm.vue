<template>
  <div v-for="(field, index) in props.fields" :key="index" class="spacing">
    <label class="form-label mt-3">{{ field.label }}</label>
    <template v-if="field.type === 'textarea'">
      <textarea
        class="form-control"
        v-model="field.value"
        :required="field.required"
        :placeholder="field.placeholder"
      ></textarea>
    </template>
    <template v-else-if="field.type === 'select'">
      <select
        class="form-control"
        v-model="field.value"
        :required="field.required"
      >
        <option v-for="(option, optionIndex) in field.options" :key="optionIndex" :value="option.value">
          {{ option.label }}
        </option>
      </select>
    </template>
    <template v-else>
      <input
        :type="field.type"
        class="form-control"
        v-model="field.value"
        :required="field.required"
        :placeholder="field.placeholder"
      />
    </template>
  </div>
</template>

<script setup>
  import { defineProps } from 'vue';
  const props = defineProps(['fields']);
</script>

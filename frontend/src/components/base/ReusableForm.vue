<template>
  <div class="container-fluid">
    <div class="row">
      <div
        v-for="(field, index) in props.fields"
        :key="index"
        :class="`col-md-${12 / props.columns}`"
      >
        <div class="spacing">
          <label class="form-label mt-3">
            {{ field.label }}
            <span v-if="field.required" class="required-asterisk">*</span>
          </label>
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
              <option
                v-for="(option, optionIndex) in field.options"
                :key="optionIndex"
                :value="option.value"
              >
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
      </div>
    </div>
  </div>
</template>

<style>
span {
  color: red;
}
</style>

<script setup>
import { defineProps } from 'vue';
const props = defineProps(['fields', 'columns']);
</script>

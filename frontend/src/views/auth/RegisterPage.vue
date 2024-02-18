<template>
  <main>
    <div id="main_content" class="col-md-6 offset-md-3 mt-3">
      <!-- <img src="@/assets/logo.jpg" class="mx-auto d-block img" alt="Task Scheduler" /> -->
      <h4 class="text-center">SALESBOOK</h4>
      <form  @submit.prevent="register">
          
          <div v-if="error" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
              <strong>{{ error_msg }}</strong>

              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

          </div>
          <div v-if="isOk" class="alert alert-success alert-dismissible fade show mt-3" role="alert">
              <strong>{{ success_msg}}</strong>

              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

          </div>
          <div>
             
              <ReusableForm :fields="formFields" :columns="2"/>
             
           </div>  
           <div class="row mt-3">
        <div class="col-12 mb-2">
            <button type="submit" class="btn btn-primary w-100" :disabled="loading">{{ loading ? 'Please wait...' : 'Register' }}</button>
          </div>
          <div class="col-12">
            <router-link to="/" class="d-block text-center">Click here if you already have an account to login</router-link>
          </div>
        </div>

       </form>
      
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import useAuthStore from '../../store';
import ReusableForm from "@/components/base/ReusableForm.vue";

// const email = ref('');
// const password = ref('test*1234');
const loading = ref(false);
const error = ref(false);
const error_msg = ref('');
const success_msg = ref('');
const isOk = ref(false)

const formFields = ref([
 
  { type: 'text', label: 'First name', databaseField: 'first_name', required: true, value:'' },
  { type: 'text', label: 'Middle name', databaseField: 'middle_name', required: false, value:'' },
  { type: 'text', label: 'Last name', databaseField: 'last_name', required: true, value:'' },
  { type: 'text', label: 'Phone number', databaseField: 'phone_number', required: true ,value:''},
  { type: 'date', label: 'Date of Birth', databaseField: 'dob', required: true ,value:''},
  { type: 'email', label: 'Email', databaseField: 'email', required: true ,value:''},
  { type: 'number', label: 'Organizational Code', databaseField: 'organization_code', required: true ,value:''},
  { type: 'password', label: 'password', databaseField: 'password', required: true ,value:''},
  { type: 'password', label: 'password', databaseField: 'password_confirmation', required: true ,value:''},
 



]);



const register = async () => {
  try {
    loading.value = true;
    const payLoad = {}; 
 
    formFields.value.forEach(field => {
      payLoad[field.databaseField] = field.value;
    })
    
    const { success, response } = await useAuthStore().register(payLoad);

    if (success) {
     
      isOk.value = true;
      success_msg.value = response;
    } else {
      error.value = true;
      error_msg.value = '';
      error_msg.value = response.response?.data?.message || response.message;
      console.log(error_msg.value)

    }
  } finally {
    loading.value = false;
  }
};


</script>

<style scoped>
  main {
    background: #F2F1F9;
  }
  #main_content {
    background: white !important;
    padding: 3em;
  }
  h6 {
    color: blue;
    cursor: pointer;
  }
</style>

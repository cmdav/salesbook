<template>
  <main>
    <div id="main_content" class="col-md-6 offset-md-3" style="margin-top:5em">
      <!-- <img src="@/assets/logo.jpg" class="mx-auto d-block img" alt="Task Scheduler" /> -->
    <h4 class="text-center">SALESBOOK</h4>
      <form  @submit.prevent="login">
          
          <div v-if="error" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
              <strong>{{ error_msg }}</strong>

              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

          </div>
          <div>
             
              <ReusableForm :fields="formFields"/>
             
           </div> 
           <div class="row mt-3">
            <div class="col-12 mb-2">
              <button type="submit" class="btn btn-primary w-100" :disabled="loading">{{ loading ? 'Please wait...' : 'Login' }}</button>
            </div>
            <div class="col-12">
              <router-link to="/register" class="d-block text-center">Click here to create an account</router-link>
            </div>
          </div>
 
          
       </form>
      
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import useAuthStore from '../../store';
import ReusableForm from "@/components/base/ReusableForm.vue";

// const email = ref('');
// const password = ref('test*1234');
const loading = ref(false);
const error = ref(false);
const error_msg = ref('');
const router = useRouter();


const formFields = ref([
 
{ type: 'email', label: 'Email', databaseField: 'email', required: true ,placeholder:'Enter your email address', value:'test2@gmail.com'},
{ type: 'password', label: 'password', databaseField: 'password', required: true ,placeholder:'Enter Password', value:'test*1234'},


]);

const login = async () => {
  try {
    loading.value = true;
    const payLoad = {}; 
 
    formFields.value.forEach(field => {
      payLoad[field.databaseField] = field.value;
    })
    
    const { success, response } = await useAuthStore().login(payLoad);

    if (success) {
      router.push('/dashboard');
    } else {
      error.value = true;
      error_msg.value = response.response?.data?.message || response.message;

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

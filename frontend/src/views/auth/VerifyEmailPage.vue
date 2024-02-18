<template>
  <main>
    <div id="main_content" class="col-md-6 offset-md-3 mt-3">
      <!-- <img src="@/assets/logo.jpg" class="mx-auto d-block img" alt="Task Scheduler" /> -->
      <h4 class="text-center">SALESBOOK</h4>
      <div v-if="loading" class="d-block text-center">
            Please wait while we verify your account ...
      </div>
      <div  v-else-if = "success && !loading">
          <div   class="alert alert-success" role="alert">
              <p>{{ resp }}</p>
          </div>
          <router-link to="/" class="d-block text-center">Proceed to login</router-link> 
      </div>
      <div  v-else >
           <div  class="alert alert-danger" role="alert">
                <p>{{ resp }}</p>   
          </div>
          <router-link to="/register" class="d-block text-center">Proceed to register</router-link> 
      </div>
         
      
      
    </div>
  </main>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { onMounted, ref } from 'vue';
import api from '../../axios';

const route = useRoute();
const token = ref('');
const loading = ref(true);
const success = ref(false);
const resp = ref('');

onMounted(async () => {
  token.value = route.params.token;
  if (token.value) {
    try {
      const response = await api.put('email-verification/' + token.value);

      if (response && response.status === 200) {
        
        success.value = true;
        resp.value = response?.data?.message || response.message ;
      } else {
        // Handle error response
        success.value = false;
        resp.value = response.data.message || 'Error occurred during email verification.';
      }
      
    } catch (err) {
      // Handle network errors or other exceptions
      success.value = false;
      resp.value = err.response?.data?.message || err.message || 'Unknown error occurred.';
    }
    loading.value = false;
  }
});
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

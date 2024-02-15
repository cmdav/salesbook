<template>
      
      <div v-if="err" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
              <strong>{{ error_msg }}</strong>
      </div>

      <!-- Modal -->
      <div class="modal  fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Edit Task</h5>
              <button type="button" class="btn-close"  data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form  @submit.prevent="editTask">
          
                  <div v-if="err" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                      <strong>{{ error_msg }}</strong>

                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                  </div>
                  <div>
                    
                      <ReusableForm :fields="formFields"/>
                    
                  </div>  
                  <div class="d-flex justify-content-between mt-3">

                      <button type="submit" class="btn btn-primary" :disabled="loading">{{ loading ? 'Please wait...' : 'Edit Task' }}</button>

                    </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
  </template>
  
  <script setup>
  import { ref, watch, defineProps } from 'vue';
  import ReusableForm from "@/components/base/ReusableForm.vue";
  // import { Modal } from 'bootstrap';
  import useTaskStore from '../../store/task.js';

  const loading = ref(false);
  const err = ref(false);
  const error_msg = ref('');
  
  const props = defineProps(['taskData','closeModal']); 
  const formFields = ref([
 
 { type: 'text', label: 'Task Name', databaseField: 'name', required: true },
 { type: 'textarea', label: 'Task Description', databaseField: 'description', required: true },
 { type: 'date', label: 'Start date', databaseField: 'start_time', required: true },
 { type: 'date', label: 'End date', databaseField: 'end_time', required: true },
 { type: 'select', label: 'Status', databaseField: 'task_status', required: true ,
    options: [
          { label: 'Pending', value: "Pending" },
          { label: 'Cancelled', value: "Cancelled" },
          { label: 'Complete', value: "Complete" }
        ]
    },


]);

  const editTask = async () => {
      const payLoad = {}; 
  
      formFields.value.forEach(field => {
        payLoad[field.databaseField] = field.value;
      })
      
      const{ success, response} = await useTaskStore().editTask(payLoad, props.taskData.id);
      if(success){
        props.closeModal();
      
       
      }else{
        err.value = true;
       error_msg.value = response.data?.message || response.message;
      }

  };

  watch(() => props.taskData, () => {
   
  if (props.taskData) {
    
    formFields.value.forEach(field => {
      field.value = props.taskData[field.databaseField];
    });
  }

});
  </script>
  
  
  
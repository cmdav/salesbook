

<template>
       
    <section>
      <div v-if="err" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
              <strong>{{ error_msg }}</strong>


      </div>
      <table class="table">
        <thead>
          <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Start time</th>
              <th scope="col">End time</th>
              <th scope="col">Status</th>
              <th scope="col">Edit</th>
              <th scope="col">Delete</th>

          </tr>
        </thead>
        <tbody v-if="tasks && tasks.length > 0">
            <tr v-for="(task, index) in tasks" :key="task.id">
              <td>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
              <td>{{ task.name }}</td>
              <td>{{ task.description }}</td>
              <td> {{ task.start_time }}</td>
              <td>{{ task.end_time }}</td>
              <td :style="{ color: getStatusColor(task.task_status) }">{{ task.task_status }}</td>
              <td><i class="fas fa-pencil-alt" style="color:green;"  @click="showModal(task)" data-bs-target="#editModal"></i></td>
              <td><i class="fas fa-trash-alt" style="color: red;" @click="deleteTask(task.id)"></i></td>
            </tr>
        </tbody>
        <tbody v-else>
          <tr >
            <td colspan="8" style="color:red"><strong>No task has been created </strong></td>
          </tr>
        </tbody>
      </table>
      
      <div  class="pagination">
        <ul  v-for="( link ) in links" :key="link.label">
          <li  class="page-item" @click="getTask(link.label)">
            <router-link to="#">{{ getFormattedLabel(link.label) }}</router-link>
          </li>
        </ul>
      </div>
      <edit-task-component :taskData="selectedTask" :closeModal="closeModal" />
      
        
      
    </section>

</template>

<script setup>

  import { ref, onMounted} from 'vue';
  import { Modal } from 'bootstrap';
  import useTaskStore from '../../store/task.js';
  import EditTaskComponent from "@/components/task/EditTaskComponent.vue";
  const tasks = ref('');
  const links = ref('');
 
  
  const currentPage = ref(1); 
  const itemsPerPage = ref(1);
  const totalPage = ref(1);
  const err = ref(false);
  const error_msg = ref('');
  const myModal = ref('');
  const selectedTask = ref(null);


const closeModal = () => {
    if (myModal.value) {
      myModal.value.hide();
    }
  };

  const showModal = (task)=>{
    selectedTask.value = task;
    myModal.value = new Modal(document.getElementById('editModal'));
    myModal.value.show()
    
       
  }

  const getFormattedLabel = (label) => {
   
    if (label === "&laquo; Previous") {
       return 'Previous';
    }
    else if (label === "Next &raquo;") {
       return 'Next';
    } else {
       return label;
     }
  };


  const getStatusColor = (status) => {
  switch (status) {
    case 'pending':
      return 'yellow';
    case 'completed':
      return 'green';
    case 'cancelled':
      return 'red';
    default:
      return 'black'; // Default color
  }
};
  const pageNo = (page)=>{

      if (page === "&laquo; Previous") {
        
        if(currentPage.value > 2){
            page = currentPage.value -1;
        }else{
            page = totalPage.value;
        }
    }
    else if (page === "Next &raquo;") {
      
        if(currentPage.value < totalPage.value){
            page = currentPage.value + 1;
          }else{
            page = 1;
          }
    }
    return page;

  }
  const getTask = async (page) => {
      page = pageNo(page);
      const { success, res } = await useTaskStore().fetchTask(page);
      if (success) {
          tasks.value = res.data;
          links.value = res.links;
          itemsPerPage.value = res.per_page; // record per page
          currentPage.value = res.current_page; // current page no
          totalPage.value = res.total; // total pages
      }
      else {
      err.value = true;
       error_msg.value = res.data?.message || res.message;

    }
};

const deleteTask = async (taskId) => {
      
      const { success } = await useTaskStore().deleteTask(taskId);
      if (success) {
        console.log(success)
        getTask(1)
       
      }
      else {
        //console.log(taskId);
      //   err.value = true;
      //  error_msg.value = response.data?.message || response.message;

    }
};

onMounted(async (page = 1) => {
  getTask(page);
  
});
  

</script>

<style>
section{
margin-top:1em;
background:white;
}

</style>

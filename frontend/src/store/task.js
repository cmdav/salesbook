import { defineStore } from 'pinia'
import api from '../axios';

const useTaskStore = defineStore({
    id: 'task_id',
    state: () => ({
        tasks: []
      }),
    
      getters: {
        getTasks(state) {
            return state.tasks
        }
    },
    actions: {
        async fetchTask(page) {
            
          try {
            const response = await api.get('tasks', {
              params: { page },
            });
    
            if (response.status === 200) {
              this.tasks = response.data;
              
                return { success: true, res: this.tasks };
            } else {
                return { success: false, res: 'Login successful' };
            }
          } catch (error) {
            return { success: false, res: error };
          }
        },
        async postTask(payLoad) {
            try {
              const response = await api.post('tasks', payLoad);
        
              if (response && response.status === 201) {
                    // console.log(response.data)
                    this.tasks.data.unshift(response.data);
                    return { success: true, response: 'Submisssion succesful' };
              } else {
                   console.log(response);
                    return { success: false, response: 'Error in data submitted' };
              }
            } catch (error) {
                  console.log(error);
                 return { success: false, response: error};
            }
          },

          async deleteTask(taskId) {
            try {
                const response = await api.delete(`tasks/${taskId}`);
        
              if (response && response.status === 200) {

                        const newTasks = { ...this.tasks };
                        newTasks.data = newTasks.data.filter(task => task.id !== taskId);
                        this.tasks.data = newTasks;
                     
                    return { success: true, response: 'data deleted successfully' };
              } else {
                   console.log(response);
                    return { success: false, response: 'deletion fail' };
              }
            } catch (error) {
                  console.log(error);
                 return { success: false, response: error};
            }
          },
          async editTask(payLoad,taskId) {
          
           
            try {
               
                const response = await api.put(`tasks/${taskId}`,payLoad);
        
              if (response && response.status === 200) {

                const index = this.tasks.data.findIndex(task => task.id === taskId);

                if (index !== -1) {
                  // Update the task with the new data
                  this.tasks.data[index] = response.data;
                }
              
                    return { success: true, response: 'data deleted successfully' };
              } else {
                   console.log(response);
                    return { success: false, response: 'update fail' };
              }
            } catch (error) {
                  console.log(error);
                 return { success: false, response: error};
            }
          },
      
      },
    });
export default useTaskStore;

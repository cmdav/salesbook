

<template>
       
     <h6>Hello {{ user.first_name}}</h6>
     <header>
          <i class="fas fa-bars"  @click="toggleNav"></i>
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Search For item" aria-label="Recipient's username" aria-describedby="basic-addon2">
            <span class="input-group-text" id="basic-addon2">Search</span>
          </div>
          
     </header>
     
  </template>
  
  <script>
  import useAuthStore from '../store';
  export default {
    name: 'HeaderComponent',
    data() {
    return {
      isNavOpen: true,
      user:''

    };
  },
  methods: {
    toggleNav() {
      
      this.isNavOpen = !this.isNavOpen;
      this.$nextTick(() => {
        const navElement = document.querySelector('nav');
        navElement.style.display = this.isNavOpen ? 'block' : 'none';
      });
    },
    checkNavOpenClass() {
    
      const navElement = document.querySelector('#container nav');
      const displayPropertyValue = window.getComputedStyle(navElement).getPropertyValue('display');
      this.isNavOpen =(displayPropertyValue === 'block') ? true: false;

    },
  },
 
  mounted() {
   
    this.checkNavOpenClass();
    this.user = useAuthStore().user;
   

  }
}
  
  </script>
  
  <style scoped>
  
  </style>
  


<template>
  <div  id="container">
    <!--nav element-->
    <NavComponent />
    <!--main element--> 
    
    <main>
      <HeaderComponent />
      <SectionComponent />
    </main>
  </div>
</template>

<script>
import NavComponent from "@/components/NavComponent.vue";
import HeaderComponent from "@/components/HeaderComponent.vue";
import SectionComponent from "@/components/SectionComponent.vue";



export default {
  name: 'UserDashboard',
  components:{
    NavComponent,
    HeaderComponent,
    SectionComponent
  }
  
}
</script>


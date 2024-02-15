<template>
  <nav>
    <!-- <img src="@/assets/logo.jpg" alt="Task Scheduler" class="img"/> -->
    <h4 style="color:white; text-align: center; margin:1em">SALESBOOK</h4>
    <hr style="color:red">
   
    <router-link to="/dashboard">
      <i class="fas fa-tachometer-alt"></i> Dashboard
    </router-link>

    <router-link to="/store">
      <i class="fas fa-store"></i> Store
    </router-link>

    <router-link to="/product">
      <i class="fas fa-box"></i> Product
    </router-link>

    <router-link to="/product-category">
      <i class="fas fa-tags"></i> Product Category
    </router-link>
    <router-link to="/customers">
      <i class="fas fa-users"></i> Customers
    </router-link>

    <router-link to="/suppliers">
      <i class="fas fa-truck"></i> Suppliers
    </router-link>

    <router-link to="/record-sales">
      <i class="fas fa-money-bill-alt"></i> Record Sales
    </router-link>

    <router-link to="/report">
      <i class="fas fa-chart-bar"></i> Report
    </router-link>
    <router-link to="#"  @click="logout">
      <i class="fas fa-sign-out-alt"></i> Log Out
    </router-link>
  </nav>
</template>

<script>
import useAuthStore from '../store';
export default {
  name: 'NavComponent',
  methods: {
    logout() {
      useAuthStore().logout();
    },
  },
}
</script>

<style scoped>

</style>

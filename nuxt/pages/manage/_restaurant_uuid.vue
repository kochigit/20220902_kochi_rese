<template>
  <div class="manage" v-if="restaurant">
    Restaurant For Manager <br><br>
    {{restaurant}}
  </div>
</template>

<script>
export default {
  middleware: 'checkAuthAtManage',
  data() {
    return {
      restaurant: null,
    }
  },
  methods: {
    async getManagedRestaurant() {
      const gotData = await this.$axios.get(`/v1/management/managedRestaurant/${this.$route.params.restaurant_uuid}`);
      console.log(gotData);
      this.restaurant = gotData.data.restaurant;
    },
  },
  created() {
    this.getManagedRestaurant();
  },
}
</script>
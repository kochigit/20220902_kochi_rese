<template>
  <div class="manager" v-if="managedRestaurants">
    <div class="managed-restaurants">
      <h2>管理中の店舗</h2>
      <div class="unmanaged-tile" v-for="management in managedRestaurants" :key="management.id">
        <img :src="'http://localhost:8000/' + management.restaurant.img_path" class="tile-img" />
        <div class="tile-info">
          <h3 class="tile-name">{{ management.restaurant.name }}</h3>
          <span class="tile-tag">#{{ management.restaurant.area }}</span>
          <span class="tile-tag">#{{ management.restaurant.genre }}</span>
          <span class="tile-tag" v-if="management.approved_at">承認済み</span>
          <span class="tile-tag" v-else>未承認！！！</span>
          <p class="tile-uuid">店舗ID：{{management.restaurant.uuid}}</p>
          <NuxtLink :to="'/manage/' + management.restaurant_uuid" class="to-restaurant-for-manager">予約確認・店舗情報変更ページへ</NuxtLink>
        </div>
      </div>
    </div>
    <div class="create-restaurant">
      店舗を作成する
      {{unmanagedRestaurants}}
    </div>
    <div class="unmanaged-restaurants">
      <h2>店舗代表者登録</h2>
      <transition-group name="card" tag="div">
        <div class="unmanaged-tile" v-for="restaurant in restaurants" :key="restaurant.uuid" :data-index="restaurant">
          <img :src="'http://localhost:8000/' + restaurant.img_path" class="tile-img" />
          <div class="tile-info">
            <h3 class="tile-name">{{ restaurant.name }}</h3>
            <span class="tile-tag">#{{ restaurant.area }}</span>
            <span class="tile-tag">#{{ restaurant.genre }}</span>
            <p class="tile-uuid">店舗ID：{{restaurant.uuid}}</p>
            <button class="request-button" @click="requestManagement(restaurant.uuid)">店舗代表者の登録を申請する</button>
          </div>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
export default {
  middleware: 'checkAuthority',
  data() {
    return {
      restaurants: null,
      managedRestaurants: null,
      unmanagedRestaurants: null,
    }
  },
  methods: {
    async getManager() {
      const gotData = await this.$axios.get('/v1/management/manager');
      console.log(gotData);
      this.managedRestaurants = gotData.data.manager.managements
    },
    async getRestaurants() {
      const gotData = await this.$axios.get('/v1/management/restaurant');
      console.log(gotData);
      this.restaurants = gotData.data.restaurants.filter(rst => !rst.managements.some((mng) => mng.user_uuid === this.$auth.user.uuid));
      // this.managedRestaurants = gotData.data.restaurants.filter(rst => rst.managements.some((mng) => mng.user_uuid === this.$auth.user.uuid));
    },
    async requestManagement(uuid) {
      const management = {
        user_uuid: this.$auth.user.uuid,
        restaurant_uuid: uuid
      };
      try {
        const response = await this.$axios.post('/v1/management', management);
        console.log(response);
        this.getRestaurants();
      } catch (error) {
        alert(error)
      }
    },
  },
  created() {
    this.getManager();
    this.getRestaurants();
  },
}
</script>

<style>
.managed-restaurants, .create-restaurant {
  margin-bottom: 50px;
}

.to-restaurant-for-manager {
  display: inline-block;
  background: coral;
  color: white;
  border-radius: 5px;
  padding: 6px 10%;
  font-size: 14px;
  box-shadow: 1px 1px 3px gray;
}


.unmanaged-tile {
  display: flex;
  background: white;
  margin-bottom: 10px;
  border-radius: 5px;
  box-shadow: 2px 2px 5px lightgray;
  padding: 5px;
}
.tile-img {
  width: 15vw;
  border-radius: 5px;
  margin-right: 10px;
}
.request-button {
  background: lightseagreen;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 6px 10%;
  font-size: 14px;
  box-shadow: 1px 1px 3px gray;
}
</style>
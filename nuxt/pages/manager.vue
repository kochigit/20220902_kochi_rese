<template>
  <div class="manager" v-if="managedRestaurants">

    <div class="managed-restaurants">
      <h2 class="manager__title">管理中の店舗</h2>
      <transition-group name="card" tag="div">
        <div class="restaurant-tile" v-for="management in managedRestaurants" :key="management.id" :data-index="management">
          <img :src="management.restaurant.img_path" class="tile-img" />
          <div class="tile-info">
            <div class="tile-name-approved-wrap">
              <h3 class="tile-name">{{ management.restaurant.name }}</h3>
              <span class="tile-tag approved" v-if="management.approved_at">承認済み</span>
              <span class="tile-tag unapproved" v-else>未承認</span>
            </div>
            <span class="tile-tag">#{{ management.restaurant.area }}</span>
            <span class="tile-tag">#{{ management.restaurant.genre }}</span>
            <p class="tile-uuid">店舗ID：{{management.restaurant.uuid}}</p>
            <NuxtLink :to="'/manage/' + management.restaurant_uuid" class="to-restaurant-for-manager">予約確認・店舗情報編集ページへ</NuxtLink>
          </div>
        </div>
      </transition-group>
      <p v-if="!managedRestaurants[0]">店舗代表者になっている店舗はありません。</p>
    </div>

    <div class="create-restaurant">
      <button @click="toggleModal" class="create-button">店舗を新しく作成する</button>
      <transition name="fade">
      <div class="create-restaurant-modal" v-if="isShow" @click.self="toggleModal">
        <div class="create-restaurant-info">
          <validation-observer ref="obs" v-slot="ObserverProps">
            <table>
              <tr>
                <th>画像</th>
                <td class="arrow">
                  <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                </td>
                <td class="new-info">
                  <validation-provider rules="required|image" ref="provider" v-slot="{ errors }" name="画像">
                    <input type="file" @change="imageSelected" class="input-img">
                  <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                  </validation-provider>
                </td>
              </tr>
              <transition name="fade-in">
                <tr v-if="url" class="preview">
                  <th></th>
                  <td></td>
                  <td>
                    <img :src="url">
                  </td>
                </tr>
              </transition>
              <tr>
                <th>店舗名</th>
                <td class="arrow">
                  <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                </td>
                <td class="new-info">
                  <validation-provider v-slot="{errors}" rules="required|max:30">
                    <input type="text" v-model="name" name="店舗名">
                    <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                  </validation-provider>
                </td>
              </tr>
              <tr>
                <th>エリア</th>
                <td class="arrow">
                  <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                </td>
                <td class="new-info">
                  <validation-provider v-slot="{errors}" rules="required|max:20">
                    <input type="text" v-model="area" name="エリア">
                    <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                  </validation-provider>
                </td>
              </tr>
              <tr>
                <th>ジャンル</th>
                <td class="arrow">
                  <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                </td>
                <td class="new-info">
                  <validation-provider v-slot="{errors}" rules="required|max:20">
                    <input type="text" v-model="genre" name="ジャンル">
                    <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                  </validation-provider>
                </td>
              </tr>
              <tr class="previous-description">
                <th>紹介文</th>
                <td class="arrow">
                  <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                </td>
                <td class="new-info">
                  <validation-provider v-slot="{errors}" rules="required|max:300">
                    <textarea v-model="description" rows="10" name="紹介文"></textarea>
                    <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                  </validation-provider>
                </td>
              </tr>
            </table>
            <button @click="createRestaurant" class="create-button" :disabled="ObserverProps.invalid || !ObserverProps.validated">店舗を新規作成する</button>
          </validation-observer>
        </div>
      </div>
    </transition>
    </div>

    <div class="unmanaged-restaurants">
      <h2 class="manager__title" >店舗代表者登録</h2>
      <button @click="unmanagedActive = !unmanagedActive" class="unmanaged-restaurants-button">他店舗一覧の表示切替</button>

      <transition name="fade">
        <div v-show="unmanagedActive">
          <transition-group name="card" tag="div">
            <div class="restaurant-tile" v-for="restaurant in restaurants" :key="restaurant.uuid" :data-index="restaurant">
              <img :src="restaurant.img_path" class="tile-img" />
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
      </transition>
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
      isShow: false,
      name: null,
      area: null,
      genre: null,
      description: null,
      image: null,
      url: null,
      unmanagedActive: false,
    }
  },
  methods: {
    async getManager() {
      const gotData = await this.$axios.get('/v1/management/manager');
      this.managedRestaurants = gotData.data.manager.managements;
    },
    async getRestaurants() {
      const gotData = await this.$axios.get('/v1/management/restaurant');
      this.restaurants = gotData.data.restaurants.filter(rst => !rst.managements.some((mng) => mng.user_uuid === this.$auth.user.uuid));
    },
    async requestManagement(uuid) {
      const management = {
        user_uuid: this.$auth.user.uuid,
        restaurant_uuid: uuid
      };
      try {
        const response = await this.$axios.post('/v1/management', management);
        this.managedRestaurants = response.data.manager.managements;
        const index = this.restaurants.findIndex((rst) => rst.uuid === uuid);
        this.restaurants.splice(index, 1);
      } catch (error) {
        alert(error);
      }
    },
    toggleModal() {
      this.isShow = !this.isShow
    },
    imageSelected(event) {
      this.$refs.provider.validate(event)
      this.image = event.target.files[0];
      if (this.image) {
        this.url = URL.createObjectURL(this.image);
      } else {
        this.url = null;
      }
    },
    
    async createRestaurant() {
      const boo = confirm(`店舗を新規作成しますか？`);
      if (boo == false) {
        return;
      }
      const newRestaurant = new FormData();
      newRestaurant.set('image', this.image);
      newRestaurant.set('name', this.name);
      newRestaurant.set('area', this.area);
      newRestaurant.set('genre', this.genre);
      newRestaurant.set('description', this.description);
      
      try {
        const response = await this.$axios.post('/v1/restaurant/', newRestaurant);
        this.managedRestaurants = response.data.manager.managements;
        this.toggleModal();
        this.image = null;
        this.name = null;
        this.area = null;
        this.genre = null;
        this.description = null;
        this.url = null;
      } catch (error) {
        alert(error);
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
.managed-restaurants {
  margin-bottom: 50px;
}
.manager__title {
  font-size: 18px;
  margin-bottom: 10px;
}
.to-restaurant-for-manager {
  display: inline-block;
  background: coral;
  color: white;
  border-radius: 5px;
  padding: 6px 10%;
  font-size: 14px;
  box-shadow: 1px 1px 3px gray;
  position: absolute;
  bottom: 10px;
  right: 10px;
}
.restaurant-tile {
  display: flex;
  background: white;
  margin-bottom: 10px;
  border-radius: 5px;
  box-shadow: 2px 2px 5px lightgray;
  padding: 5px;
  position: relative;
}
.tile-img {
  width: 130px;
  border-radius: 5px;
  margin: auto 10px auto 0;
  height: fit-content;
  object-fit: contain;
}
.tile-info {
  width: 100%;
}
.tile-info *:not(a){
  padding-bottom: 5px;
  margin-right: 10px;
}
.tile-tag {
  display: inline-block;
}
.request-button {
  background: lightseagreen;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 6px 10%;
  font-size: 14px;
  box-shadow: 1px 1px 3px gray;
  position: absolute;
  bottom: 10px;
  right: 0;
}
.create-button {
  background: royalblue;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 6px 10%;
  font-size: 15px;
  box-shadow: 1px 1px 3px gray;
  display: block;
  margin: 0 auto;
}
.create-restaurant {
  margin-bottom: 100px;
}
.create-restaurant-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.85);
  color: white;
  display: flex;
  flex-flow: column;
  justify-content: center;
  align-items: center;
  z-index: 20;
}
.right-arrow {
  width: 30px;
  vertical-align: middle;
}
.create-restaurant-info {
  width: 90%;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 5px;
  padding: 20px 30px 30px;
  box-shadow: 0 0 7px gainsboro;
}
.create-restaurant-info table {
  margin-bottom: 20px;
}
.create-restaurant-info th {
  width: 10%;
  text-align: left;
}
.new-info {
  width: 70%;
  padding: 5px 0;
}
.arrow {
  width: 1%;
  padding-right: 20px;
}
.create-restaurant-info table * {
  vertical-align: middle;
  line-height: 1.2;
}
.new-info input {
  border: none;
  border-radius: 5px;
  padding: 5px 5px;
  font-size: 15px;
  width: 100%;
  background: whitesmoke;
  color: black;
  outline-color: royalblue;
}
input.input-img {
  font-size: 13px;
}
.new-info textarea {
  border: none;
  border-radius: 5px;
  resize: none;
  width: 100%;
  font-size: 15px;
  padding: 5px 5px;
  background: whitesmoke;
  outline-color: royalblue;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.7s;
}
.fade-enter, .fade-leave-to{
  opacity: 0;
}

.fade-in-enter-active {
  transition: opacity 1s;
}
.fade-in-enter{
  opacity: 0;
}
.fade-in-leave-active {
  display: none;
}
.preview img {
  width: 100%;
  max-height: 40vh;
  object-fit: contain;
}

.error--lightcoral {
  color: lightcoral;
  font-size: 14px;
  margin-top: 2px;
}
.unmanaged-restaurants-button {
  background: lightseagreen;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 6px 10%;
  font-size: 15px;
  box-shadow: 1px 1px 3px gray;
  display: block;
  margin: 30px auto;
}
.tile-tag.approved {
  color: lightseagreen;
  margin: 0;
  font-size: 12px;
  border: lightseagreen 1px solid;
  padding: 4px 10px 3px;
  border-radius: 20px;
}
.tile-tag.unapproved {
  color: crimson;
  margin: 0;
  font-size: 12px;
  border: crimson 1px dashed;
  padding: 4px 10px 3px;
  border-radius: 20px;
}
.tile-uuid {
  margin-bottom: 30px;
}

.card-enter-active, .card-move {
  transition: opacity 1s, transform 1s;
}
.card-leave-active {
  position: absolute;
  opacity: 0.3;
  transition: opacity 1s transform 1s;
  right: -30%;
  bottom: -30%;
}
.card-enter {
  opacity: 0;
  transform: translateY(-35px);
}
.card-leave-to {
  opacity: 0;
}
.tile-name-approved-wrap {
  display: flex;
  justify-content: space-between;
  margin-top: 5px;
}


@media screen and (max-width: 768px) { 
  .tile-img {
    width: 30vw;
    max-width: 130px;
  }
  .right-arrow {
    width: 20px;
  }
  .create-restaurant-info {
    padding: 10px;
  }
  .create-restaurant-info th {
    font-weight: normal;
  }
  .create-restaurant-info td {
    padding: 5px 0;
  }
  .create-restaurant-info * {
    font-size: 14px !important;
  }
  .create-restaurant {
    margin-bottom: 50px;
  }
  .manager__title {
    margin-top: 20px;
  }
  .tile-uuid {
    font-size: 13px;
    margin-bottom: 50px;
  }
  .to-restaurant-for-manager {
    padding: 6px 10px;
  }
  .request-button {
    padding: 6px 10px;
  }
}
</style>
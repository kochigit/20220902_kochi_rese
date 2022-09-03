<template>
  <div class="manage" v-if="restaurant">
    <transition name="fade">
      <div class="edit-restaurant-modal" v-if="isShow" @click.self="toggleEdit">
        <div class="edit-restaurant-img">
          <input type="file" @change="imageSelected" class="update-img">
          <div class="flex--sb">
            <img :src="'http://localhost:8000/' + restaurant.img_path" class="previous-img" />
            <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
            <transition name="fade-in">
              <div v-if="url" class="preview">
                <img :src="url">
              </div>
            </transition>
              <div v-if="!url" class="no-preview">
              </div>
          </div>
          <button @click="imageUpload" class="upload-button" :disabled="!image">画像を変更する</button>
        </div>
        <div class="edit-restaurant-info">
          <table>
            <tr>
              <th>店舗名</th>
              <td class="previous-info">
                {{ restaurant.name }}
              </td>
              <td class="arrow">
                <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
              </td>
              <td class="new-info">
                <input type="text" v-model="name">
              </td>
            </tr>
            <tr>
              <th>エリア</th>
              <td class="previous-info">
                {{ restaurant.area }}
              </td>
              <td class="arrow">
                <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
              </td>
              <td class="new-info">
                <input type="text" v-model="area">
              </td>
            </tr>
            <tr>
              <th>ジャンル</th>
              <td class="previous-info">
                {{ restaurant.genre }}
              </td>
              <td class="arrow">
                <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
              </td>
              <td class="new-info">
                <input type="text" v-model="genre">
              </td>
            </tr>
            <tr class="previous-description">
              <th>紹介文</th>
              <td class="previous-info">
                {{ restaurant.description }}
              </td>
              <td class="arrow">
                <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
              </td>
              <td class="new-info">
                <textarea v-model="description" rows="10"></textarea>
              </td>
            </tr>
          </table>
          <button @click="updateInfo" class="upload-button">店舗情報を変更する</button>
        </div>
      </div>
    </transition>
    <div class="flex--sb">
      <h2 class="manage__title--restaurant">店舗情報</h2>
      <span class="edit-restaurant pointer" @click="toggleEdit">
        <img src="~assets/img/iconmonstr-pencil-8-black.svg" class="edit pointer"  />
        編集
      </span>
    </div>
    <div class="managed-restaurant">
      <img :src="'http://localhost:8000/' + restaurant.img_path" class="managed-restaurant-img" />
      <div class="managed-restaurant-info">
        <p class="managed-restaurant-uuid">店舗ID：{{restaurant.uuid}}</p>
        <p class="managed-restaurant-name">店舗名：{{ restaurant.name }}</p>
        <p class="managed-restaurant-tag">エリア：{{ restaurant.area }}</p>
        <p class="managed-restaurant-tag">ジャンル：{{ restaurant.genre }}</p>
        <p class="restaurant-description">紹介文：<br>{{ restaurant.description }}</p>
      </div>
    </div>
    <div class="reservations">
      <h2 class="manage__title--reservation">予約一覧</h2>
      <div class="reservation-for-manager" v-for="reservation in restaurant.reservations" :key="reservation.id">
        <div class="">
          <p class="reservation-id">予約ID：{{reservation.id}}</p>
          <p>日付：{{$dayjs(reservation.date).format('YYYY/MM/DD')}}</p>
          <p>時刻：{{reservation.time.substr(0, 5).replace(":", "：")}}</p>
          <p>人数：{{reservation.number}} 名様</p>
        </div>
        <div class="">
          <p>お名前：{{reservation.user.name}}</p>
          <p>メール：{{reservation.user.email}}</p>
          <p>ユーザーID：{{reservation.user.uuid}}</p>
        </div>
        <span></span>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    middleware: 'checkAuthAtManage',
    data() {
      return {
        restaurant: null,
        isShow: false,
        name: null,
        area: null,
        genre: null,
        description: null,
        image: null,
        url: null,
      }
    },
    methods: {
      async getManagedRestaurant() {
        const gotData = await this.$axios.get(`/v1/management/managedRestaurant/${this.$route.params.restaurant_uuid}`);
        this.restaurant = gotData.data.restaurant;
      },
      toggleEdit() {
        this.isShow = !this.isShow;
      },
      imageSelected(event) {
        if (event.target.files[0]) {
          this.image = event.target.files[0];
        }
        if (this.image) {
          this.url = URL.createObjectURL(this.image);
        }
      },
      async imageUpload() {
        
        const formData = new FormData();
        formData.set('image', this.image);
        formData.set('uuid', this.restaurant.uuid);
        try {
          const response = await this.$axios.post('/v1/restaurant-img', formData);
          this.image = null;
          this.url = null;
          alert('画像の変更が完了しました。')
          this.restaurant.img_path = response.data.img_path;
        } catch (error) {
          alert(error);
        }
      },
      async updateInfo() {
        const boo = confirm(`店舗情報を変更してもよろしいですか？`);
        if (boo == false) {
          return;
        }
        let newInfo = {};
        if (this.name) {
          newInfo.name = this.name;
        }
        if (this.area) {
          newInfo.area = this.area;
        }
        if (this.genre) {
          newInfo.genre = this.genre;
        }
        if (this.description) {
          newInfo.description = this.description;
        }
        try {
          const response = await this.$axios.put(`/v1/restaurant/${this.restaurant.uuid}`, newInfo);
          this.restaurant = response.data.restaurant;
          this.toggleEdit();
          this.name = null;
          this.area = null;
          this.genre = null;
          this.description = null;
        } catch (error) {
          alert(error);
        }
      },
    },
    created() {
      this.getManagedRestaurant();
    },
  }

</script>

<style>
.flex--sb{
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}
.manage__title--restaurant {
  font-size: 18px;
}
.edit-restaurant {
  color: gray;
}
.manage__title--reservation {
  font-size: 18px;
  margin-bottom: 10px;
}
.managed-restaurant {
  display: flex;
  background: white;
  align-items: center;
  padding: 5px 10px;
  border-radius: 5px;
  box-shadow: 2px 2px 5px lightgray;
  margin-bottom: 50px;
}
.managed-restaurant-img {
  width: 30%;
  height: fit-content;
  object-fit: contain;
  margin-right: 10px;
  border-radius: 3px;
}
.managed-restaurant-info p{
  padding: 3px 0;
  line-height: 1.3;
}

.reservation-for-manager {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  background: rgb(255, 113, 62);
  color: white;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 10px;
  box-shadow: 2px 2px 5px rgb(177, 177, 177);
}
.reservation-for-manager p {
  padding: 5px 0;
}
.reservation-id {
  text-decoration-line: underline;
  text-underline-offset: 2px;
  font-weight: bold;
}

.edit-restaurant-modal {
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

.edit-restaurant-img {
  width: 90%;
  margin-bottom: 70px;
  background: rgba(0, 0, 0, 0.8);
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 7px gainsboro;
}
.previous-img {
  width: 40%;
}
.update-img {
  font-size: 14px;
  display: block;
  margin: 0 0 10px auto;
}
.upload-button {
  display: block;
  margin: 20px auto 0;
  border: none;
  background: rgb(255, 113, 62);
  color: white;
  border-radius: 5px;
  font-size: 15px;
  padding: 5px 10px;
  box-shadow: 0 0 5px gainsboro;
}
.edit-restaurant-info {
  width: 90%;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 5px;
  padding: 15px 20px;
  box-shadow: 0 0 7px gainsboro;
}
.edit-restaurant-info th {
  width: 15%;
  text-align: left;
}
.previous-info {
  width: 40%;
  padding: 5px 0;
}
.new-info {
  width: 40%;
  padding: 5px 0;
}
.arrow {
  width: 1%;
  padding: 0 10px;
}
.edit-restaurant-info table * {
  vertical-align: middle;
  line-height: 1.2;
}

.new-info input {
  border: none;
  border-radius: 5px;
  padding: 3px 5px;
  font-size: 15px;
  width: 100%;
  background: whitesmoke;
}
.new-info textarea {
  border: none;
  border-radius: 5px;
  resize: none;
  width: 100%;
  background: whitesmoke;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.7s;
}
.fade-enter, .fade-leave-to{
  opacity: 0;
}

.fade-in-enter-active {
  transition: opacity 0.7s;
}
.fade-in-enter{
  opacity: 0;
}
.fade-in-leave-active {
  display: none;
}


.preview {
  width: 40%;
}
.preview img {
  width: 100%;
}
.no-preview {
  width: 40%;
}
</style>
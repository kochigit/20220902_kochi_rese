<template>
  <div class="manage" v-if="restaurant">
    <transition name="fade">
      <div class="qr-reader" v-if="qrActive" @click.self="qrActive=!qrActive">
        <div class="camera">
          <qrcode-stream :paused="paused" @decode="onDecode" @init="onInit"/>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div class="qrReservation-modal" v-if="qrReservation" @click.self="closeQrModal">
        <div class="qrReservation reservation-for-manager" :class="qrReservation.visited_at ? 'visited': null">
          <img src="~assets/img/checkbox-marked-circle-plus-outline.svg" class="check-img pointer" @click="makeReservationVisited(qrReservation.id)" v-if="!qrReservation.visited_at">
          <span class="check--hover">ご来店済みにする</span>
          <div class="reservation-info">
            <p class="reservation-id">予約ID：{{qrReservation.id}}</p>
            <p>日付：{{$dayjs(qrReservation.date).format('YYYY/MM/DD')}}</p>
            <p>時刻：{{qrReservation.time.substr(0, 5).replace(":", "：")}}</p>
            <p>人数：{{qrReservation.number}} 名様</p>
          </div>
          <div class="reservation-user">
            <p v-if="qrReservation.visited_at" class="visited-at">
            <img src="~assets/img/checkbox-marked-circle.svg" class="visited-img">
            {{$dayjs(qrReservation.visited_at).format('YYYY/M/D HH:mm')}} ご来店済み
          </p>
            <p>お名前：{{qrReservation.user.name}}</p>
            <p>メール：{{qrReservation.user.email}}</p>
            <p>ユーザーID：{{qrReservation.user.uuid}}</p>
          </div>
        </div>
      </div>
    </transition>

    <transition name="fade">
      <div class="edit-restaurant-modal" v-if="isShow" @click.self="toggleEdit">
        <div class="edit-restaurant-img">
          <input type="file" @change="imageSelected" class="update-img">
          <div class="flex--sb">
            <img :src="restaurant.img_path" class="previous-img" />
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
      <h2 class="manage__title">店舗情報</h2>
      <span class="edit-restaurant pointer" @click="toggleEdit">
        <img src="~assets/img/iconmonstr-pencil-8-black.svg" class="edit-img pointer"/>
        編集
      </span>
    </div>
    <div class="managed-restaurant">
      <img :src="restaurant.img_path" class="managed-restaurant-img"/>
      <div class="managed-restaurant-info">
        <p class="managed-restaurant-uuid">店舗ID：{{restaurant.uuid}}</p>
        <p class="managed-restaurant-name">店舗名：{{ restaurant.name }}</p>
        <p class="managed-restaurant-tag">エリア：{{ restaurant.area }}</p>
        <p class="managed-restaurant-tag">ジャンル：{{ restaurant.genre }}</p>
        <p class="restaurant-description">紹介文：<br>{{ restaurant.description }}</p>
      </div>
    </div>

    <div class="emailing">
      <button class="email-button" @click="mailActive = true">ユーザーにメールを送信する</button>
      <transition name="fade">
        <div class="send-email-modal" v-if="mailActive" @click.self="mailActive = false">
          <div class="create-email-content">
            <validation-observer ref="obs" v-slot="ObserverProps">
              <table>
                <tr>
                  <th>送信先</th>
                  <td class="arrow">
                    <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                  </td>
                  <td class="content checkbox">
                    <validation-provider v-slot="{errors}" rules="required">
                      <input type="checkbox" v-model="sendTo" name="送信先" id="visited" value="visited" class="pointer">
                      <label for="visited">ご来店済み</label>
                      <input type="checkbox" v-model="sendTo" name="送信先" id="reserved" value="reserved" class="pointer">
                      <label for="reserved">ご予約済み（未来店）</label>
                      <input type="checkbox" v-model="sendTo" name="送信先" id="liked" value="liked" class="pointer">
                      <label for="liked">お気に入り登録済み</label>
                      <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                    </validation-provider>
                  </td>
                </tr>
                <tr>
                  <th>タイトル</th>
                  <td class="arrow">
                    <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                  </td>
                  <td class="content title">
                    <validation-provider v-slot="{errors}" rules="required|max:30">
                      <input type="text" v-model="title" name="タイトル">
                      <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                    </validation-provider>
                  </td>
                </tr>
                <tr class="previous-description">
                  <th>本文</th>
                  <td class="arrow">
                    <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
                  </td>
                  <td class="content body">
                    <validation-provider v-slot="{errors}" rules="required|max:2000">
                      <textarea v-model="body" rows="10" name="本文"></textarea>
                      <p class="error--lightcoral" v-show="errors[0]">{{errors[0]}}</p>
                    </validation-provider>
                  </td>
                </tr>
              </table>
              <button @click="sendEmails" class="email-button" :disabled="ObserverProps.invalid || !ObserverProps.validated">メールを送信する</button>
            </validation-observer>
          </div>
        </div>
      </transition>
    </div>

    <div class="reservations">
      <div class="flex--sb">
        <h2 class="manage__title">予約一覧</h2>
        <span class="scan pointer" @click="qrActive = !qrActive">
          <img src="~assets/img/qrcode-scan.svg" class="scan-img">
          QR
        </span>
      </div>
      <div class="reservation-for-manager" v-for="reservation in reservations" :key="reservation.id" :class="reservation.visited_at ? 'visited': null">
        <img src="~assets/img/checkbox-marked-circle-plus-outline.svg" class="check-img pointer" @click="makeReservationVisited(reservation.id)" v-if="!reservation.visited_at">
        <span class="check--hover">手動でご来店済みにする</span>
        <div class="reservation-info">
          <p class="reservation-id">予約ID：{{reservation.id}}</p>
          <p>日付：{{$dayjs(reservation.date).format('YYYY/MM/DD')}}</p>
          <p>時刻：{{reservation.time.substr(0, 5).replace(":", "：")}}</p>
          <p>人数：{{reservation.number}} 名様</p>
        </div>
        <div class="reservation-user">
          <p v-if="reservation.visited_at" class="visited-at">
            <img src="~assets/img/checkbox-marked-circle.svg" class="visited-img">
            {{$dayjs(reservation.visited_at).format('YYYY/M/D HH:mm')}} ご来店済み
          </p>
          <p>お名前：{{reservation.user.name}}</p>
          <p>メール：{{reservation.user.email}}</p>
          <p>ユーザーID：{{reservation.user.uuid}}</p>
        </div>
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
        reservations: null,
        isShow: false,
        name: null,
        area: null,
        genre: null,
        description: null,
        image: null,
        url: null,
        paused: false,
        qrActive: false,
        qrReservation: null,
        mailActive: false,
        sendTo: [],
        title: null,
        body: null,
        
      }
    },
    methods: {
      async getManagedRestaurant() {
        const gotData = await this.$axios.get(`/v1/management/managedRestaurant/${this.$route.params.restaurant_uuid}`);
        this.restaurant = gotData.data.restaurant;
        this.reservations = gotData.data.restaurant.reservations.sort((a, b) => (a.time < b.time) ? -1 : 1).sort((a, b) => (a.date < b.date) ? -1 : 1);
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
      async onInit (promise) {
        // show loading indicator
        try {
          await promise
          // successfully initialized
        } catch (error) {
          if (error.name === 'NotAllowedError') {
            // user denied camera access permisson
          } else if (error.name === 'NotFoundError') {
            // no suitable camera device installed
          } else if (error.name === 'NotSupportedError') {
            // page is not served over HTTPS (or localhost)
          } else if (error.name === 'NotReadableError') {
            // maybe camera is already in use
          } else if (error.name === 'OverconstrainedError') {
            // passed constraints don't match any camera. Did you requested the front camera although there is none?
          } else {
            // browser is probably lacking features (WebRTC, Canvas)
          }
        } finally {
          // hide loading indicator
        }
      },
      onDecode(qrData){
        this.paused = true;
        const qrReservation = JSON.parse(qrData);
        this.qrActive = false;
        if (qrReservation.restaurant_uuid !== this.restaurant.uuid) {
          setTimeout(() => {
            alert('この店舗の予約ではありません。');
          }, 100);
          return;
        }
        this.qrReservation = this.reservations.find(rsv => rsv.id === qrReservation.id);
      },
      closeQrModal() {
        if (this.qrReservation.visited_at) {
          this.qrReservation = null;
          return;
        }
        const boo = confirm('この予約を来店済みにせずにこのウィンドウを閉じますか？\n※ 原則来店済みにしてください。');
        if (boo) {
          this.qrReservation = null;
        }
      },
      async makeReservationVisited(id) {
        try {
          const response = await this.$axios.put(`/v1/reservation/${id}`, {
            visited_at: this.$dayjs().format('YYYY-MM-DD HH:mm:ss')
          });
          const index = this.restaurant.reservations.findIndex((rsv) => rsv.id === id);
          this.restaurant.reservations[index].visited_at = response.data.reservation.visited_at;
        } catch (error) {
          alert(`エラーが発生しました。サーバー管理者にお問い合わせください。\n${error}`)
        }
      },
      async sendEmails() {
        const boo = confirm(`メールを送信しますか？`);
        if (boo == false) {
          return;
        }
        const email = {
          uuid: this.restaurant.uuid,
          name: this.restaurant.name,
          sendTo: this.sendTo,
          title: this.title,
          body: this.body,
        }
        try {
          await this.$axios.post('/v1/management/email', email);
          alert('メール送信が完了しました');
          this.mailActive = false;
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
.manage__title {
  font-size: 18px;
}
.edit-restaurant {
  color: gray;
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
  padding: 10px 20px;
  border-radius: 5px;
  margin-bottom: 10px;
  box-shadow: 2px 2px 5px rgb(177, 177, 177);
  position: relative;
}
.reservation-for-manager p {
  padding: 5px 0;
}
.reservation-id {
  text-decoration-line: underline;
  text-underline-offset: 2px;
  font-weight: bold;
  margin-bottom: 10px;
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
  padding: 15px 20px 20px;
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

.qrReservation-modal {
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
.qrReservation {
  background: coral;
  width: 90%;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 8px gainsboro;
  position: relative;
}
.check-img {
  width: 30px;
  position: absolute;
  top: 15px;
  right: 20px;
}
.check--hover {
  font-size: 12px;
  border: white 1px dashed;
  position: absolute;
  padding: 3px 5px 3px 6px;
  border-radius: 10px 10px 0 10px;
  top: 0;
  right: 0;
  transform: translate(-53px, 3px);
  opacity: 0;
  transition: 0.3s;
}
.check-img:hover+.check--hover {
  opacity: 1;
  transition: 0.3s;
}

.visited-img, .scan-img {
  width: 26px;
}
.reservation-for-manager.visited {
  background: rgb(24, 158, 151);
}
.visited-at {
  text-align: right;
  line-height: 23px;
}
.reservation-info {
  width: 30%;
}
.reservation-user {
  width: 70%;
}
.scan {
  color: gray;
  line-height: 23px;
}
.qr-reader {
  position: fixed;
  background: rgba(0, 0, 0, 0.9);
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 20;
  display: flex;
  justify-content: center;
  align-items: center;
}
.camera {
  width: 70%;
}

.emailing {
  margin-bottom: 50px;
}
.email-button {
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
.send-email-modal {
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
.create-email-content {
  width: 90%;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 5px;
  padding: 20px 30px 30px;
  box-shadow: 0 0 7px gainsboro;
}
.create-email-content table {
  margin-bottom: 20px;
}
.create-email-content th {
  width: 10%;
  text-align: left;
}
.content {
  width: 70%;
  padding: 5px 0;
}
.arrow {
  width: 1%;
  padding-right: 20px;
}
.create-email-content table * {
  vertical-align: middle;
  line-height: 1.2;
}
.content.title input {
  border: none;
  border-radius: 5px;
  padding: 5px 5px;
  font-size: 15px;
  width: 100%;
  background: whitesmoke;
  color: black;
  outline-color: royalblue;
}
.content.checkbox label {
  margin-right: 16px;
  font-size: 15px;
  cursor: pointer;
}
.content.checkbox input:checked + label {
  color: lightskyblue;
  font-weight: bold;
}
input.input-img {
  font-size: 13px;
}
.content textarea {
  border: none;
  border-radius: 5px;
  resize: none;
  width: 100%;
  font-size: 15px;
  padding: 5px 5px;
  background: whitesmoke;
  outline-color: royalblue;
}
.error--lightcoral {
  color: lightcoral;
  font-size: 14px;
  margin-top: 2px;
}
</style>
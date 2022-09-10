<template>
  <div class="mypage" v-if="reservations || favorites">

    <transition name="fade"> 
      <div class="edit-modal" v-if="isShow" @click.self="toggleEdit">
        <validation-observer ref="obs" v-slot="ObserverProps" class="edit-validation">
          <table class="my-reservation__table edit-table">
            <div class="table__top">
              <div class="reservation-number-wrap">
                <img src="~assets/img/reserved-svgrepo-com.svg" class="reserved-img" />
                <h3 class="reservation-number">予約 {{ rIndex }}</h3>
              </div>
              <div class="edit-and-cancel">
                <img src="~assets/img/iconmonstr-x-mark-square-lined.svg" class="close pointer" @click="toggleEdit" />
                <span class="close--hover">編集画面を閉じる</span>
              </div>
            </div>
            <tr>
              <th>Restaurant</th>
              <td>{{ currentRsv.restaurant.name }}</td>
            </tr>
            <tr>
              <th>Date</th>
              <td>
                {{ $dayjs(currentRsv.date).format("YYYY年 M月 D日 (dd)") }}
              </td>
              <td>
                <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
              </td>
              <td>
                <validation-provider name="明日" hidden>
                  <input type="date" v-model="today" />
                </validation-provider>
                <validation-provider v-slot="{ errors }" rules="after:@明日">
                  <input type="date" name="予約日" v-model="date" class="reservation__input"
                    :min="$dayjs().add(1, 'day').format('YYYY-MM-DD')" />
                  <p class="error--orange" v-if="errors[0]">
                    ※ {{ errors[0] }}
                  </p>
                </validation-provider>
              </td>
            </tr>
            <tr>
              <th>Time</th>
              <td>{{ currentRsv.time.substr(0, 5).replace(":", "：") }}</td>
              <td>
                <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
              </td>
              <td>
                <input type="time" name="予約時刻" v-model="time" class="reservation__input w-100 time" step="1200"
                  list="'12:00', '14:00'" />
              </td>
            </tr>
            <tr>
              <th>Number</th>
              <td>{{ currentRsv.number }} 名様</td>
              <td>
                <img src="~assets/img/chevron-triple-right.svg" class="right-arrow" />
              </td>
              <td>
                <validation-provider v-slot="{ errors }" rules="min_value:1|max_value:20|integer">
                  <input type="number" name="人数" v-model="number" class="reservation__input w-100 number" min="1"
                    max="20" data-format="様" />
                  <p class="error--orange" v-if="errors[0]">
                    ※ {{ errors[0] }}
                  </p>
                </validation-provider>
              </td>
            </tr>
          </table>
          <button class="reserve-button update-button" @click="updateReservation" :disabled="ObserverProps.invalid || manualDisabler">
            予約を変更する
          </button>
        </validation-observer>
      </div>
    </transition>

    <div class="mypage__welcome-wrap">
      <h2 class="mypage__welcome">いらっしゃいませ、{{ $auth.user.name }}様</h2>
        <div v-if="!$auth.user.email_verified_at" class="is-email-verified">
          <span class="unverified">メールアドレス未認証：</span>
          <button @click="sendEmailVerification">認証メールを送信</button>
        </div>
        <div v-else class="is-email-verified">
          <span class="verified">メールアドレス認証済</span>
        </div>
      </div>
    <div class="myreservation-and-myfavorite">
      <div class="my-reservation">
        <h2 class="my-any__title">予約状況</h2>
        <table class="my-reservation__table" v-for="(rsv, index) in reservations" :key="index" :class="{visited:!isVisited(rsv.date, rsv.time)}">
          <div class="table__top">
            <div class="reservation-number-wrap">
              <img src="~assets/img/reserved-svgrepo-com.svg" class="reserved-img" />
              <h3 class="reservation-number">予約 {{ index + 1 }}</h3>
              <p v-if="!isVisited(rsv.date, rsv.time)">：来店済み</p>
            </div>
            <div class="edit-and-cancel" v-if="isVisited(rsv.date, rsv.time)">
              <img src="~assets/img/iconmonstr-pencil-8.svg" class="edit pointer" @click="toggleEdit(index + 1, rsv)" />
              <span class="edit--hover">編集する</span>
              <img src="~assets/img/iconmonstr-x-mark-11.svg" class="cancel pointer"
                @click="cancel(index + 1, rsv.id)" />
              <span class="cancel--hover">キャンセルする</span>
            </div>
            <div class="evaluate" v-else>
              <img src="~assets/img/Comments icon 6.svg" class="comment-img pointer">
              <span class="comment--hover">口コミを書く</span>
            </div>
          </div>
          <tr>
            <th>Restaurant</th>
            <td>{{ rsv.restaurant.name }}</td>
          </tr>
          <tr>
            <th>Date</th>
            <td>{{ $dayjs(rsv.date).format("YYYY年 M月 D日 (dd)") }}</td>
          </tr>
          <tr>
            <th>Time</th>
            <td>{{ rsv.time.substr(0, 5).replace(":", "：") }}</td>
          </tr>
          <tr>
            <th>Number</th>
            <td>{{ rsv.number }} 名様</td>
          </tr>
          <div class="evaluate-box" v-if="!isVisited(rsv.date, rsv.time)">
            <div class="evaluated" v-if="rsv.evaluation">
              <div class="graded">
                <p>評価</p>
                <span class="star--yellow">★</span>
                <span :class="(rsv.evaluation.grade>1)? 'star--yellow': null">★</span>
                <span :class="(rsv.evaluation.grade>2)? 'star--yellow': null">★</span>
                <span :class="(rsv.evaluation.grade>3)? 'star--yellow': null">★</span>
                <span :class="(rsv.evaluation.grade>4)? 'star--yellow': null">★</span>
              </div>
              <p class="comment-label">コメント</p>
              <p>{{rsv.evaluation.comment}}</p>
            </div>
            <div class="not-evaluated" v-else>
              <validation-observer ref="obs" v-slot="ObserverProps">
                <validation-provider rules="required">
                  <div class="grade">
                    <input id="star5" type="radio" v-model="grade" value="5">
                    <label for="star5">★</label>
                    <input id="star4" type="radio" v-model="grade" value="4">
                    <label for="star4">★</label>
                    <input id="star3" type="radio" v-model="grade" value="3">
                    <label for="star3">★</label>
                    <input id="star2" type="radio" v-model="grade" value="2">
                    <label for="star2">★</label>
                    <input id="star1" type="radio" v-model="grade" value="1">
                    <label for="star1">★</label>
                    <p>評価</p>
                  </div>
                </validation-provider>
                <validation-provider v-slot="{errors}" rules="required">
                  <label for="comment" class="comment-label">コメント</label>
                  <textarea v-model="comment" id="comment" name="コメント" class="comment" rows="5"></textarea>
                  <p class="error--orange">{{errors[0]}}</p>
                </validation-provider>
                <button @click="evaluate(rsv)" class="evaluate-button" :disabled="ObserverProps.invalid || !ObserverProps.validated">投稿</button>
              </validation-observer>
            </div>
          </div>
          <div v-else class="qrcode">
            <qrcode :value="rsv|toJSON" :options="{width: 150}"/>
          </div>
        </table>
        <p v-if="!reservations[0]">予約はありません。</p>
      </div>

      <div class="my-favorite">
        <h2 class="my-any__title">お気に入り店舗</h2>
        <div v-if="favorites">
          <div class="favorite-card-wrap flex-wrap">
            <div class="favorite-card" v-for="fav in favorites" :key="fav.id">
              <img :src="'http://localhost:8000/' + fav.restaurant.img_path" class="card-img" />
              <div class="card-info">
                <h2 class="card-name">{{ fav.restaurant.name }}</h2>
                <span class="card-tag">#{{ fav.restaurant.area }}</span>
                <span class="card-tag">#{{ fav.restaurant.genre }}</span>
                <div class="todetail-and-favorite">
                  <NuxtLink :to="'/detail/' + fav.restaurant.uuid" class="to-detail">
                    詳しく見る
                  </NuxtLink>
                  <img src="~assets/img/iconmonstr-favorite-3.svg" class="favorite" @click="unlike(fav)" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <p v-if="!favorites[0]">お気に入り店舗はありません。</p>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    middleware: 'auth',
    data() {
      return {
        uuid: this.$auth.user.uuid,
        reservations: null,
        favorites: null,
        isShow: false,
        rIndex: null,
        currentRsv: null,
        date: null,
        time: null,
        number: null,
        today: this.$dayjs().format("YYYY-MM-DD"),
        now: this.$dayjs().add(+1, 'hours').format('HH:mm'),
        grade: null,
        comment: null,
      };
    },
    computed: {
      manualDisabler() {
        if (!this.date && !this.time && !this.number) {
          return true;
        } else {
          return false;
        }
      },
    },
    filters: {
      toJSON(value) {
        return JSON.stringify(value);
      }
    },
    methods: {
      async getUserWithReservationsAndFavorites() {
        const gotData = await this.$axios.get(`/v1/user/${this.uuid}`);
        this.reservations = gotData.data.user.reservations.sort((a, b) => (a.date > b.date) ? -1 : 1);
        this.favorites = gotData.data.user.favorites.reverse();
      },
      async cancel(num, id) {
        const boo = confirm(`予約${num}をキャンセルしますか？`);
        if (boo) {
          try {
            await this.$axios.delete(`/v1/reservation/${id}`);
            alert(`予約${num}がキャンセルされました。`);
            const rsvIndex = this.reservations.findIndex((rsv) => rsv.id === id);
            this.reservations.splice(rsvIndex, 1);
          } catch (error) {
            alert(error);
          }
        }
      },
      async unlike(fav) {
        const boo = confirm(
          `「${fav.restaurant.name}」 をお気に入りから外しますか？`
        );
        if (boo) {
          try {
            await this.$axios.delete(`/v1/favorite/${fav.id}`);
            const favIndex = this.favorites.findIndex(
              (favo) => favo.id === fav.id
            );
            this.favorites.splice(favIndex, 1);
          } catch (error) {
            return;
          }
        }
      },
      toggleEdit(index, rsv) {
        if (this.isShow == false) {
          this.isShow = true;
          this.rIndex = index;
          this.currentRsv = rsv;
        } else {
          this.isShow = false;
        }
      },
      async updateReservation() {
        const boo = confirm(`予約を変更してもよろしいですか？`);
        if (boo==false) {
          return;
        }
        let rsvData = {};
        if (this.date) {
          rsvData.date = this.date;
        }
        if (this.time) {
          rsvData.time = this.time;
        }
        if (this.number) {
          rsvData.number = this.number;
        }
        try {
          const gotData = await this.$axios.put(`/v1/reservation/${this.currentRsv.id}`, rsvData);
          const rsvIdx = this.reservations.findIndex((rsv) => rsv.id === this.currentRsv.id);
          const newRsv = gotData.data.reservation;
          this.reservations[rsvIdx].date = newRsv.date;
          this.reservations[rsvIdx].time = newRsv.time;
          this.reservations[rsvIdx].number = newRsv.number;
          this.toggleEdit();
        } catch (error) {
          alert(error);
        }
      },
      isVisited(date, time) {
        return date > this.today || (date == this.today && time > this.now);
      },
      async evaluate(rsv) {
        const evaluation = {
          reservation_id: rsv.id,
          grade: this.grade,
          comment: this.comment
        }
        try {
          const gotData = await this.$axios.post('/v1/evaluation', evaluation);
          console.log(gotData.data.evaluation);
          rsv.evaluation = gotData.data.evaluation;
        } catch (error) {
          alert(error);
        }
      },
      async sendEmailVerification() {
        try {
          await this.$axios.get('/auth/email-verify');
          alert('認証メールを送信しました。\n1時間以内に認証を行ってください。');
        } catch (error) {
          alert(error);
        }
      },
    },
    created() {
      this.getUserWithReservationsAndFavorites();
    },
  };

</script>

<style>
  .mypage__welcome {
    font-size: 20px;
  }

  .myreservation-and-myfavorite {
    display: flex;
    justify-content: space-between;
  }

  .my-reservation {
    width: 40%;
  }

  .my-favorite {
    width: 55%;
  }

  .my-any__title {
    margin-bottom: 30px;
    font-size: 18px;
  }

  .my-reservation__table {
    background: #3c53ff;
    color: white;
    border-radius: 5px;
    width: 100%;
    text-align: left;
    display: block;
    padding: 20px 30px;
    margin-top: 20px;
    box-shadow: 2px 2px 7px gray;
  }

  .table__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .reservation-number-wrap {
    display: flex;
    align-items: center;
  }

  .reserved-img {
    width: 32px;
    margin-right: 10px;
  }

  .reservation-number {
    font-weight: normal;
    margin-right: 5px;
  }

  .edit {
    width: 24px;
    margin-right: 17px;
  }

  .edit--hover, .cancel--hover, .comment--hover {
    font-size: 12px;
    border: white 1px dashed;
    position: absolute;
    padding: 3px 5px 3px 6px;
    border-radius: 10px 10px 0 10px;
    transform: translate(-105px, -12px);
    opacity: 0;
    transition: 0.3s;
  }

  .edit:hover+.edit--hover {
    opacity: 1;
    transition: 0.3s;
  }
  .cancel {
    width: 26px;
  }
  .cancel--hover, .comment--hover {
    border-radius: 10px 0 10px 10px;
    transform: translate(-127px, 32px);
  }
  .cancel:hover+.cancel--hover {
    opacity: 1;
    transition: 0.3s;
  }
  .comment-img {
    width: 32px;
  }
  .comment--hover {
    transform: translate(-120px, 26px);
  }
  .comment-img:hover+.comment--hover {
    opacity: 1;
  }



  .my-reservation__table th {
    font-weight: normal;
    padding: 10px 0;
  }

  .my-reservation__table td {
    padding-left: 40px;
  }

  .favorite-card {
    width: 47%;
    background: white;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 2px 2px 5px gray;
  }

  .edit-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 20;
  }

  .close {
    width: 30px;
  }

  .close--hover {
    font-size: 12px;
    border: white 1px dashed;
    position: absolute;
    padding: 3px 5px 3px 6px;
    border-radius: 10px 10px 0 10px;
    transform: translate(-140px, -15px);
    opacity: 0;
    transition: 0.3s;
  }

  .close:hover+.close--hover {
    opacity: 1;
    transition: 0.3s;
  }

  .edit-table {
    margin-top: 0;
    width: fit-content;
    box-shadow: none;
    border-radius: 8px 8px 0 0;
  }

  .right-arrow {
    width: 30px;
    vertical-align: middle;
  }

  .update-button {
    border-radius: 0 0 8px 8px;
  }

  .edit-validation {
    border-radius: 8px;
    box-shadow: 0 0 8px white;
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.7s;
  }

  .fade-enter,
  .fade-leave-to{
    opacity: 0;
  }

  .visited {
    background: #5c6394;
  }

.evaluate-box {
  margin-top: 30px;
}
.grade {
  display: flex;
  flex-flow: row-reverse;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 15px;
}
.grade input[type=radio] {
  display: none;
}
.grade label {
  font-size: 26px;
}
.grade label:first-of-type {
  margin-right: 3vw;
}
.grade label:hover {
  color: #ffcc00;
}
.grade label:hover ~ label {
  color: #ffcc00;
}
.grade input[type=radio]:checked ~ label {
  color: #ffcc00;
}
.grade p {
  margin-right: 4vw;
}

.comment-label {
  display: block;
  margin-bottom: 7px;
}
.comment {
  width: 100%;
  resize: none;
  border-radius: 5px;
  border: none;
}
.evaluate-button {
  background: #344cff;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 7px 18px;
  font-size: 12px;
  display: block;
  margin: 10px 0 0 auto;
  box-shadow: 1px 1px 3px gray;
}


.graded {
  margin-bottom: 15px;
  display: flex;
  align-items: baseline;
  justify-content: space-between;
}
.graded p {
  margin-right: 3vw;
}
.graded span {
  font-size: 26px;
}
.graded span:last-of-type {
  margin-right: 4vw;
}
.star--yellow {
  color: #ffcc00;
}

.is-email-verified {
  font-size: 14px;
  display: flex;
  align-items: center;
}
.is-email-verified button {
  background: #344cff;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 7px 18px;
  font-size: 12px;
  box-shadow: 1px 1px 3px gray;
}
.mypage__welcome-wrap{
  margin-top: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}
.verified {
  color: lightseagreen;
  font-size: 12px;
  border: lightseagreen 1px solid;
  padding: 3px 6px;
  border-radius: 10px;
}
.unverified {
  font-weight: bold;
}
.qrcode {
  color: #344cff;
}
</style>

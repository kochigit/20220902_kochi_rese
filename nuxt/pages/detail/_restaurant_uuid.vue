<template>
  <div class="detail flex-wrap" v-if="restaurant">
    <div class="restaurant">
      <div class="restaurant__top">
        <button @click="$router.go(-1)" class="back">
          <img src="~assets/img/iconmonstr-arrow-64.svg" class="back-img" />
        </button>
        <h2 class="restaurant-name">{{ restaurant.name }}</h2>
      </div>
      <img
        :src="'http://localhost:8000/' + restaurant.img_path"
        class="restaurant-img"
      />
      <span class="restaurant-tag">#{{ restaurant.area }}</span>
      <span class="restaurant-tag">#{{ restaurant.genre }}</span>
      <p class="restaurant-description">{{ restaurant.description }}</p>
    </div>

    <validation-observer ref="obs" v-slot="ObserverProps" class="reservation">
      <div class="reservation-form">
        <h2 class="reservation__title">予約</h2>
        <validation-provider v-slot="{ errors }" rules="required">
          <input
            type="date"
            name="予約日"
            v-model="date"
            class="reservation__input"
            :min="$dayjs().format('YYYY-MM-DD')"
          />
          <p class="error--orange" v-if="errors[0]">※{{ errors[0] }}</p>
        </validation-provider>
        <validation-provider v-slot="{ errors }" rules="required">
          <input
            type="time"
            name="予約時刻"
            v-model="time"
            class="reservation__input w-100 time"
          />
          <p class="error--orange" v-if="errors[0]">※ {{ errors[0] }}</p>
        </validation-provider>
        <validation-provider
          v-slot="{ errors }"
          rules="required|min_value:1|max_value:20|integer"
        >
          <input
            type="number"
            name="人数"
            v-model="number"
            class="reservation__input w-100 number"
            min="1"
            max="20"
          />
          <p class="error--orange" v-if="errors[0]">※ {{ errors[0] }}</p>
        </validation-provider>
        <table class="confirmation-table">
          <tr>
            <th>Restaurant</th>
            <td>{{ restaurant.name }}</td>
          </tr>
          <tr>
            <th>Date</th>
            <td>{{ dateDayjs }}</td>
          </tr>
          <tr>
            <th>Time</th>
            <td>{{ time.replace(":", "：") }}</td>
          </tr>
          <tr>
            <th>Number</th>
            <td v-if="number">{{ number }} 名様</td>
          </tr>
        </table>
      </div>
      <button
        class="reserve-button"
        @click="reserve"
        :disabled="ObserverProps.invalid || !ObserverProps.validated"
      >
        予約する
      </button>
    </validation-observer>
  </div>
</template>

<script>
export default {
  data() {
    return {
      restaurant: null,
      uuid: this.$route.params.restaurant_uuid,
      date: null,
      time: '',
      number: null,
    };
  },
  computed: {
    dateDayjs() {
      if (
        this.$dayjs(this.date).format("YYYY年 M月 D日 (dd)") == "Invalid Date"
      ) {
        return null;
      } else {
        return this.$dayjs(this.date).format("YYYY年 M月 D日 (dd)");
      }
    },
  },
  methods: {
    async getRestaurant() {
      const gotData = await this.$axios.get(`/v1/restaurant/${this.uuid}`);
      this.restaurant = gotData.data.restaurant;
    },
    async reserve() {
      if (!this.$auth.loggedIn) {
        const boo = confirm("先にログインしてください。\nログインしますか？");
        if (boo) {
          this.$router.push("/login");
        }
        return;
      }
      const reservation = {
        user_uuid: this.$auth.user.uuid,
        restaurant_uuid: this.uuid,
        date: this.date,
        time: this.time,
        number: this.number,
      };
      try {
        await this.$axios.post("/v1/reservation", reservation);
        this.$router.push("/done");
      } catch (error) {
        alert(error);
      }
    },
  },
  created() {
    this.getRestaurant();
  },
};
</script>

<style>
.detail {
  position: relative;
  top: -66px;
}

.restaurant {
  width: 46%;
  text-shadow: 1px 1px 2px rgba(128, 128, 128, 0.6);
}

.restaurant__top {
  display: flex;
  align-items: center;
  margin-bottom: 25px;
  margin-top: 81px;
}

.back {
  background: white;
  border: none;
  margin-right: 20px;
  border-radius: 4px;
  box-shadow: 1px 1px 5px gray;
  width: 24px;
  height: 24px;
}

.back-img {
  width: 10px;
  vertical-align: middle;
}

.restaurant-name {
  font-size: 24px;
}

.restaurant-img {
  width: 100%;
  margin-bottom: 25px;
}

.restaurant-tag {
  display: inline-block;
  padding-right: 5px;
  margin-bottom: 25px;
}

.restaurant-description {
  line-height: 1.4;
}

.reservation {
  background: #3c53ff;
  color: white;
  width: 46%;
  border-radius: 5px;
  box-shadow: 2px 2px 7px gray;
  display: flex;
  flex-flow: column;
  justify-content: space-between;
}

.reservation-form {
  padding: 30px;
}

.reservation__title {
  font-size: 22px;
  margin-bottom: 30px;
}

.reservation__input {
  display: block;
  padding: 3px 10px;
  border: none;
  border-radius: 5px;
  margin: 12px 0;
  font-size: 14px;
  background: white;
}
.reservation__input:placeholder-shown {
  color: gainsboro;
}
.w-100 {
  width: 100%;
  padding: 2px 10px;
}

.number {
  padding: 1px 10px;
}

.confirmation-table {
  background: #4576fd;
  border-radius: 5px;
  width: 100%;
  text-align: left;
  display: block;
  padding: 20px;
  margin-top: 20px;
}

.confirmation-table th {
  font-weight: normal;
  padding: 10px 0;
}

.confirmation-table td {
  padding-left: 40px;
}

.reserve-button {
  width: 100%;
  border: none;
  border-radius: 0 0 5px 5px;
  background: #0926ff;
  color: white;
  padding: 16px 0;
  font-size: 14px;
  font-weight: bold;
  transition: 0.8s;
  text-shadow: 0 0 2px black;
}

.reserve-button:disabled {
  opacity: 1;
  color: rgba(255, 255, 255, 0.5);
  text-shadow: none;
  background: #4576fd;
}

.error--orange {
  color: rgb(255, 217, 0);
  font-size: 14px;
}
</style>
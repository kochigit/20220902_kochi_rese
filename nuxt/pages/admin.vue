<template>
  <div class="admin">
    <div class="create-manager flex">
    <div class="create-manager-box">
      <h2 class="create-manager__title">Create Restaurant Manager</h2>
      <validation-observer ref="obs" v-slot="ObserverProps">
        <form @submit.prevent="registerManager" class="create-manager__form">
          <div class="input">
            <validation-provider v-slot="{errors}" rules="required|min:4|alpha_dash|max:191">
              <p><label>
                <img src="~assets/img/Registration icon 1.svg" class="name-img">
                <input type="text" v-model="name" placeholder="Username" name="ユーザーネーム">
              </label></p>
              <div class="error">{{errors[0]}}</div>
            </validation-provider>
            <validation-provider v-slot="{errors}" rules="required|email|max:191">
              <p><label>
                <img src="~assets/img/Email.svg" class="email-img">
                <input type="email" v-model="email" placeholder="Email" name="メールアドレス">
              </label></p>
              <p class="error">{{errors[0]}}</p>
            </validation-provider>
            <validation-provider v-slot="{errors}" rules="required|min:8|max:191">
              <p><label>
                <img src="~assets/img/Key icon.svg" class="lock-img">
                <input type="password" v-model="password" placeholder="Password" name="パスワード">
              </label></p>
              <p class="error">{{errors[0]}}</p>
            </validation-provider>
          </div>
          <button type="submit" :disabled="ObserverProps.invalid || !ObserverProps.validated" class="button create-manager__button">店舗代表者を作成する</button>
        </form>
      </validation-observer>
    </div>
  </div>
  </div>
</template>

<script>
export default {
  middleware: ['auth','checkAuthority'],
  data() {
    return {
      name: null,
      email: null,
      password: null,
    };
  },
  methods: {
    async registerManager() {
      try {
        const profile = {
          name: this.name,
          email: this.email,
          password: this.password,
        };
        await this.$axios.post("/v1/user", profile);
        alert('Manager Created !!')
      } catch (err) {
        console.log(err);
        alert('このメールアドレスは使用済みです。\n詳細：'+err.message);
      }
    },
  },
}
</script>


<style>
.create-manager {
  height: 50vh;
}
.create-manager-box {
  background: white;
  border-radius: 7px;
  box-shadow: 2px 2px 7px gray;
  width: 400px;
}
.create-manager__title {
  background: lightslategray;
  color: white;
  font-size: 18px;
  padding: 18px 20px;
  border-radius: 7px 7px 0 0;
  letter-spacing: 1px;
}
.create-manager__form {
  padding: 20px 30px;
}
.create-manager__form label {
  display: flex;
  justify-content: space-between;
}
.name-img, .email-img, .lock-img {
  width: 22px;
  position: relative;
}
.create-manager__form input {
  border: none;
  border-bottom: gray 1px solid;
  font-size: 15px;
  width: 90%;
}
.create-manager__form p {
  padding: 5px 0;
}
.create-manager__button {
  background: lightslategray;
  color: white;
  border: none;
  border-radius: 5px;
  padding: 7px 18px;
  font-size: 12px;
  display: block;
  margin: 10px 0 0 auto;
  box-shadow: 1px 1px 3px gray;
}

.error {
  color: rgb(255, 77, 77);
  font-size: 13px;
  font-weight: bold;
}
</style>
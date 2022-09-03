<template>
  <div class="login-register flex">
    <div class="login-register-box">
      <h2 class="login-register__title">Registration</h2>
      <validation-observer ref="obs" v-slot="ObserverProps">
        <form @submit.prevent="register" class="login-register__form">
          <div class="input">
            <validation-provider v-slot="{errors}" rules="required|min:4|alpha_dash|max:191">
              <p><label>
                <img src="~assets/img/Registration icon 1.svg" class="name-img">
                <input type="text" v-model="name" placeholder="Username" name="ユーザーネーム">
              </label></p>
              <p class="error">{{errors[0]}}</p>
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
          <button type="submit" :disabled="ObserverProps.invalid || !ObserverProps.validated" class="button login-register__button">登録</button>
        </form>
      </validation-observer>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      name: null,
      email: null,
      password: null,
    };
  },
  methods: {
    async register() {
      try {
        const profile = {
          name: this.name,
          email: this.email,
          password: this.password,
        };
        await this.$axios.post("auth/register", profile);
        this.$router.push('/thank')
      } catch (err) {
        console.log(err);
        alert('このメールアドレスは使用済みです。\n詳細：'+err.message);
      }
    },
  },
};
</script>

<style>
.name-img, .email-img, .lock-img {
  width: 22px;
  position: relative;
}

</style>
<template>
  <div>
    <div class="login-register flex">
      <div class="login-register-box">
        <h2 class="login-register__title">Login</h2>
        <validation-observer ref="obs" v-slot="ObserverProps">
          <form @submit.prevent="login" class="login-register__form">
            <div class="input">
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
            <button type="submit" :disabled="ObserverProps.invalid || !ObserverProps.validated" class="button login-register__button">ログイン</button>
          </form>
        </validation-observer>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: null,
      password: null,
    };
  },
  methods: {
    async login() {
      try {
        await this.$auth.loginWith("laravelJWT", {
          data: {
            email: this.email,
            password: this.password,
          }
        });
        this.$router.push("/");
      } catch (err) {
        alert('メールアドレスまたはパスワードが間違っているか、ユーザーが存在しないか、サーバーのエラーです。\n詳細：'+err.message);
      }
    },
  },
  created() {
  }
};
</script>

<style>
.login-register {
  height: 50vh;
}
.login-register-box {
  background: white;
  border-radius: 7px;
  box-shadow: 2px 2px 7px gray;
  width: 400px;
}
.login-register__title {
  background: #344cff;
  color: white;
  font-size: 18px;
  padding: 18px 20px;
  border-radius: 7px 7px 0 0;
  letter-spacing: 1px;
}
.login-register__form {
  padding: 20px 30px;
}
.login-register__form label {
  display: flex;
  justify-content: space-between;
}
.email-img, .lock-img {
  width: 22px;
  position: relative;
}
.login-register__form input {
  border: none;
  border-bottom: gray 1px solid;
  font-size: 15px;
  width: 90%;
}
.login-register__form p {
  padding: 5px 0;
}
.login-register__button {
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

.error {
  color: rgb(255, 77, 77);
  font-size: 13px;
  font-weight: bold;
}
</style>
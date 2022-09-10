<template>
  <div class="email-verification">
    <p>メールアドレス認証中......</p>
    <transition name="fade">
      <div v-if="isShow" class="login-form">
        <div class="login-register-box">
          <h2 class="login-register__title">この端末かブラウザではログインしていません<br>ログインしてください</h2>
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
              <button type="submit" :disabled="ObserverProps.invalid || !ObserverProps.validated"
                class="button login-register__button">ログイン</button>
            </form>
          </validation-observer>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        isShow: false,
        email: null,
        password: null,
      }
    },
    methods: {
      async verifyEmail() {
        if (!this.$auth.loggedIn) {
          await setTimeout(() => {
            this.isShow = true;
          }, 800);
          return;
        }
        const hashedEmail = {
          hashedEmail: this.$route.params.hashedEmail
        };
        try {
          const response = await this.$axios.post('/auth/email-verify', hashedEmail);
          if (response.status == 200) {
            this.$store.commit('changeEmailVerifiedAt', response.data.user.email_verified_at);
            alert('メール認証が完了しました。');
            this.$router.push('/mypage');
          } else if (response.status == 204) {
            alert('メール認証の期限が過ぎました。\n再度認証メールを送信してください。');
            this.$router.push('/mypage');
          } else if (response.status == 203) {
            alert('メールアドレスはすでに認証済みです。');
            this.$router.push('/mypage');
          } 
        } catch (error) {
          alert('メールアドレスの認証ができませんでした。');
          return this.$router.push('/');
        }
      },
      async login() {
      try {
        await this.$auth.loginWith("laravelJWT", {
          data: {
            email: this.email,
            password: this.password,
          }
        });
        this.isShow = false;
        this.verifyEmail();
      } catch (err) {
        alert('メールアドレスまたはパスワードが間違っているか、ユーザーが存在しないか、サーバーのエラーです。\n詳細：'+err.message);
      }
    },
    },
    created() {
      this.verifyEmail();
    }
  }

</script>

<style>
  .login-form {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 30;
    background: rgba(0, 0, 0, 0.7);
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.7s;
  }

  .fade-enter,
  .fade-leave-to {
    opacity: 0;
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
    font-size: 16px;
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

  .email-img,
  .lock-img {
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

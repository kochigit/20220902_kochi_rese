<template>
  <div class="email-verification">
    <p>メールアドレス認証中......</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      process: null,
    }
  },
  methods: {
    async verifyEmail() {
      const hashedEmail = {
        hashedEmail: this.$route.params.hashedEmail
      }
      console.log(hashedEmail);
      try {
        const response = await this.$axios.post('/auth/email-verify', hashedEmail);
        console.log(response);
        if (response.status == 200) {
          alert('メール認証が完了しました。')
          this.$router.push('/mypage')
        } else if (response.status == 204) {
          alert('メール認証の期限が過ぎました。')
          this.$router.push('/mypage')
        } else if (response.status == 203) {
          alert('メールアドレスはすでに認証済みです。')
          this.$router.push('/mypage')
        } 
      } catch (error) {
        console.log(error);
        alert('メールアドレスの認証ができませんでした。');
        return this.$router.push('/')
      }
    }
  },
  created() {
    this.verifyEmail();
  }
}
</script>
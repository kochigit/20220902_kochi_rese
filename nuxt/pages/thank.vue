<template>
  <div class="thank flex">
    <div class="thank-box">
      <h2>会員登録ありがとうございます</h2>
      <NuxtLink to="/login" class="to-login">ログインする</NuxtLink>
    </div>
  </div>
</template>



<style>
.thank {
  height: 50vh;
}
.thank-box {
  background: white;
  border-radius: 7px;
  box-shadow: 2px 2px 7px gray;
  width: 400px;
  height: 300px;
  display: flex;
  flex-flow: column;
  justify-content: center;
  align-items: center;
}
.thank-box h2 {
  margin-bottom: 50px;
  font-size: 20px;
}
.to-login {
  background: #344cff;
  color: white;
  border-radius: 5px;
  padding: 8px 17px;
  font-size: 13px;
  box-shadow: 1px 1px 3px gray;
}


</style>
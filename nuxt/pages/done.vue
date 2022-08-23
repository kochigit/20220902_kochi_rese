<template>
  <div class="done flex">
    <div class="done-box">
      <h2>ご予約ありがとうございます</h2>
      <div class="links">
        <NuxtLink to="/" class="to-page">トップページへ</NuxtLink>
        <NuxtLink to="/mypage" class="to-page">マイページへ</NuxtLink>
      </div>
    </div>
  </div>
</template>

<style>
.done {
  height: 50vh;
}
.done-box {
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
.done-box h2 {
  margin-top: 40px;
  margin-bottom: 50px;
  font-size: 20px;
}
.to-page {
  background: #344cff;
  color: white;
  border-radius: 5px;
  padding: 8px 17px;
  font-size: 13px;
  box-shadow: 1px 1px 3px gray;
  margin-bottom: 20px;
}
.links {
  display: flex;
  flex-flow: column;
  text-align: center;
}


</style>
<template>
  <div class="not-found">
    <h2>お探しのページは見つかりませんでした。</h2>
    <NuxtLink to='/' class="to-home">ホームに戻る</NuxtLink>
  </div>
</template>

<style scoped>
  .not-found {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 50vh;
    flex-flow: column;
    font-size: 18px;
  }

  .to-home {
    margin-top: 50px;
    background: #344cff;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    box-shadow: 2px 2px 5px gray;
    font-size: 14px;
  }

</style>

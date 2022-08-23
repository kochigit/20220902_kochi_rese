<template>
  <div v-if="restaurants">
    <div class="search-bar">
      
    </div>
    <div class="restaurant-card-wrap flex-wrap">
      <div class="restaurant-card" v-for="restaurant in restaurants" :key="restaurant.id">
        <img :src="'http://localhost:8000/' + restaurant.img_path" class="card-img" />
        <div class="card-info">
          <h2 class="card-name">{{ restaurant.name }}</h2>
          <span class="card-tag">#{{ restaurant.area }}</span>
          <span class="card-tag">#{{ restaurant.genre }}</span>
          <div class="todetail-and-favorite">
            <NuxtLink :to="'/detail/' + restaurant.uuid" class="to-detail">詳しく見る</NuxtLink>
            <img src="~assets/img/iconmonstr-favorite-3.svg" class="favorite"
              @click="unlike(restaurant)" v-if="
                $auth.loggedIn
                  ? restaurant.favorites.some(
                      (fav) => fav.user_uuid === $auth.user.uuid
                    )
                  : false
              " />
            <img src="~assets/img/iconmonstr-favorite-9.svg" class="favorite" @click="like(restaurant)" v-else />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        restaurants: null,
      };
    },
    methods: {
      async getrestaurants() {
        const gotData = await this.$axios.get("/v1/restaurant");
        this.restaurants = gotData.data.restaurants;
      },
      async like(restaurant) {
        if (this.$auth.loggedIn) {
          try {
            const like = {
              user_uuid: this.$auth.user.uuid,
              restaurant_uuid: restaurant.uuid,
            };
            const favorite = await this.$axios.post("/v1/favorite", like);
            // 以下のif文で連打による配列要素の無駄な追加（null追加）を防ぐ
            if (
              restaurant.favorites.some(
                (fav) => fav.user_uuid === this.$auth.user.uuid
              )
            ) {
              return;
            } else {
              restaurant.favorites.push(favorite.data.favorite);
            }
          } catch (error) {
            alert(error);
          }
        } else {
          const boo = confirm("ログインしてください。\nログインしますか？");
          if (boo) {
            this.$router.push("/login");
          }
        }
      },
      async unlike(restaurant) {
        try {
          const findFav = restaurant.favorites.find(
            (fav) => fav.user_uuid === this.$auth.user.uuid
          );
          await this.$axios.delete(`/v1/favorite/${findFav.id}`);
          const favIndex = restaurant.favorites.findIndex(
            (fav) => fav.id === findFav.id
          );
          restaurant.favorites.splice(favIndex, 1);
        } catch (error) {
          return;
          // 赤ハート連打で404エラーは返るが、UXを保持するためにNuxt側ではそのエラーをcatchして即return; consoleには出るが妥協。
          // like(),unlike()ともに数回の連打には耐えられるが、ずっと連打されるとTooManyRequestを返されて動かなくなる。
        }
      },
    },
    created() {
      this.getrestaurants();
    },
  };

</script>

<style>
  .flex-wrap {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
  }

  .restaurant-card {
    width: 24%;
    background: white;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 2px 2px 5px gray;
  }

  .card-info {
    padding: 20px;
  }

  .card-img {
    width: 100%;
    height: 13vw;
    object-fit: cover;
    border-radius: 5px 5px 0 0;
  }

  .card-name {
    font-size: 18px;
    padding-bottom: 10px;
  }

  .card-tag {
    display: inline-block;
    font-size: 13px;
    padding-bottom: 15px;
  }

  .todetail-and-favorite {
    display: flex;
    justify-content: space-between;
  }

  .to-detail {
    background: #3c53ff;
    color: white;
    border-radius: 5px;
    padding: 6px 10%;
    font-size: 14px;
    box-shadow: 1px 1px 3px gray;
  }

</style>

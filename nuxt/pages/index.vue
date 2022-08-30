<template>
  <div v-if="restaurants" class="restaurants">
    <div class="search-bar pointer" @change="search">
      <select v-model="area" class="select">
        <option value="" class="option">All area</option>
        <option v-for="area in areas" :key="area" class="option">
          {{area}}
        </option>
      </select>
      <select v-model="genre" class="select">
        <option value="">All genre</option>
        <option v-for="genre in genres" :key="genre">
          {{genre}}
        </option>
      </select>
      <label for="word">
        <img src="~assets/img/iconmonstr-magnifier-4.svg" class="magnifier">
      </label>
      <input type="text" class="search__input" placeholder="Search ..." v-model.lazy="word" id="word">
      <img src="~assets/img/iconmonstr-x-mark-11-black.svg" class="clear pointer" @click="clear">
    </div>

    <transition-group name="card" tag="div" class="restaurant-card-wrap flex-wrap">
      <div class="restaurant-card" v-for="restaurant in restaurants" :key="restaurant.uuid" :data-index="restaurant">
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
    </transition-group>
    
    <p v-if="restaurants==''">お探しの飲食店はありませんでした。</p>

  </div>
</template>

<script>
  export default {
    data() {
      return {
        restaurants: null,
        allRestaurants: null,
        areas: [],
        genres: [],
        area: '',
        genre: '',
        word: null,
      };
    },
    methods: {
      async getrestaurants() {
        const gotData = await this.$axios.get("/v1/restaurant");
        this.restaurants = gotData.data.restaurants;
        this.allRestaurants = this.restaurants;
        this.areas = Array.from(new Set(this.restaurants.map(restaurant => restaurant.area)));
        this.genres = Array.from(new Set(this.restaurants.map(restaurant => restaurant.genre)));
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
            if (restaurant.favorites.some((fav) => fav.user_uuid === this.$auth.user.uuid)) {
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
      async search() {
        let searchQuery = {};
        if (this.area) {
          searchQuery.area = this.area;
        }
        if (this.genre) {
          searchQuery.genre = this.genre;
        }
        if (this.word) {
          searchQuery.word = this.word;
        }
        const gotData = await this.$axios.post('/v1/search-restaurant', searchQuery);
        this.restaurants = gotData.data.searchedRestaurants;
      },
      clear() {
        this.area = '';
        this.genre = '';
        this.word = null;
        this.restaurants = this.allRestaurants;
      },
    },
    created() {
      this.getrestaurants();
      console.log(this.$auth);
    },
  };

</script>

<style>
  .search-bar {
    position: absolute;
    top: 30px;
    right: 5%;
    background: white;
    box-shadow: 2px 2px 4px rgb(163, 163, 163);
    border-radius: 5px;
    padding: 6px 0px;
  }
  .search-bar * {
    border: none;
    font-weight: bold;
    font-size: 14px;
    line-height: 1.6;
    cursor: pointer;
    outline: none;
  }
  .select {
    border-right: gainsboro 1px solid;
    width: 10vw;
    padding-left: 0.8vw;
    min-width: fit-content;
    /* -webkit-appearance: none; */
    /* -moz-appearance: none; */
    /* appearance: none; */
    /* background: url('~assets/img/iconmonstr-arrow-33.svg'); */
  }
  .search__input {
    width: 19vw;
  }

  .restaurant-card-wrap::before {
    content: '';
    display: block;
    width: 24%;
    order: 1;
  }
  .restaurant-card-wrap::after {
    content: '';
    display: block;
    width: 24%;
  }

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

.card-enter-active, .card-move {
  transition: opacity 1s, transform 1s;
}
.card-leave-active {
  position: absolute;
  opacity: 0.3;
  transition: opacity 1s transform 1s;
  right: -30%;
  bottom: -30%;
}
.card-enter {
  opacity: 0;
  transform: translateY(-35px);
}
.card-leave-to {
  opacity: 0;
}

.clear {
  position: absolute;
  right: 10px;
}
.magnifier {
  width: 18px;
  vertical-align: middle;
  transform: translateY(-1px);
  margin-left: 0.5vw;
}
</style>

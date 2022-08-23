<template>
  <div class="hamburger-wrap">
    <div class="hamburger">
      <div class="hamburger__logo" @click="openMenu">
        <img src="~assets/img/Hamburger icon 7.svg" class="hamburger-img" />
      </div>
      <NuxtLink to="/">
        <h1 class="rese">Rese</h1>
      </NuxtLink>
      <p
        class="welcome"
        v-if="$auth.loggedIn"
        :class="$route.path !== '/mypage' ? 'show' : null"
      >
        いらっしゃいませ、{{ $auth.user.name }}様
      </p>
    </div>
    <nav class="slide-menu" :class="isActive">
      <div class="hamburger">
        <div class="hamburger__logo" @click="closeMenu">
          <img
            src="~assets/img/iconmonstr-x-mark-lined.svg"
            class="hamburger-img"
          />
        </div>
      </div>
      <div class="slide-menu__list-wrap flex">
        <ul class="slide-menu__list" v-if="$auth.loggedIn">
          <li class="slide-menu__content" @click="closeMenu">
            <NuxtLink to="/">Home</NuxtLink>
          </li>
          <li class="slide-menu__content pointer" @click="logout">Logout</li>
          <li class="slide-menu__content" @click="closeMenu">
            <NuxtLink to="/mypage">Mypage</NuxtLink>
          </li>
        </ul>
        <ul class="slide-menu__list" v-else>
          <li class="slide-menu__content" @click="closeMenu">
            <NuxtLink to="/">Home</NuxtLink>
          </li>
          <li class="slide-menu__content" @click="closeMenu">
            <NuxtLink to="/register">Registration</NuxtLink>
          </li>
          <li class="slide-menu__content" @click="closeMenu">
            <NuxtLink to="/login">Login</NuxtLink>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isActive: null,
    };
  },
  methods: {
    openMenu() {
      this.isActive = "active";
    },
    closeMenu() {
      this.isActive = null;
    },
    async logout() {
      const boo = confirm("ログアウトしますか？");
      if (boo) {
        try {
          await this.$auth.logout();
          this.closeMenu();
          if (this.$route.path == "/") {
            location.reload();
          } else {
            this.$router.push("/");
          }
        } catch (error) {
          alert(error);
        }
      }
    },
  },
};
</script>

<style>
.hamburger {
  display: flex;
  align-items: baseline;
  padding: 30px 0;
  width: fit-content;
}
.hamburger__logo {
  background: #3c53ff;
  padding: 6px;
  margin-right: 14px;
  border-radius: 6px;
  box-shadow: 1px 1px 5px gray;
  cursor: pointer;
}
.hamburger-img {
  width: 24px;
}
img {
  vertical-align: bottom;
}
.rese {
  color: #3c53ff;
  font-size: 26px;
  text-shadow: 1px 1px 2px rgb(185, 185, 185);
  margin-right: 20px;
}
.welcome {
  font-weight: bold;
  font-size: 15px;
  opacity: 0;
  transition: 1s;
}
.welcome.show {
  opacity: 1;
  transition: 1.5s;
  transition-delay: 1s;
}

.slide-menu {
  width: 100vw;
  height: 100vh;
  position: fixed;
  background: white;
  padding: 0 5% 0 5%;
  top: 0;
  right: -100%;
  transition: 600ms ease-out;
}
.slide-menu.active {
  right: 0;
  transition: 500ms ease-out;
}
.slide-menu__list-wrap {
  height: 60%;
  align-items: center;
}
.slide-menu__list {
  color: #3c53ff;
  font-size: 26px;
  font-weight: bold;
  text-align: center;
}
.slide-menu__content {
  margin: 40px 0;
  text-shadow: 1px 1px 2px rgb(185, 185, 185);
}
.pointer {
  cursor: pointer;
}
</style>
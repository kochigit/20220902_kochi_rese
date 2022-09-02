<template>
  <div class="hamburger-wrap">
    <div class="hamburger">
      <div class="hamburger__logo" :class="path" @click="openMenu">
        <img src="~assets/img/Hamburger icon 7.svg" class="hamburger-img" />
      </div>
      <NuxtLink to="/">
        <h1 class="rese" :class="path">Rese</h1>
      </NuxtLink>
      <transition name="welcome">
      <p class="welcome" v-if="$auth.loggedIn && ($route.path !== '/mypage')">
        いらっしゃいませ、{{ $auth.user.name }}様
      </p>
      </transition>
    </div>
    <nav class="slide-menu" :class="isActive">
      <div class="hamburger">
        <div class="hamburger__logo" :class="path" @click="closeMenu">
          <img src="~assets/img/iconmonstr-x-mark-lined.svg" class="hamburger-img">
        </div>
      </div>
      <div class="slide-menu__list-wrap flex">
        <ul class="slide-menu__list" :class="path" v-if="$auth.loggedIn">
          <li class="slide-menu__content" @click="closeMenu">
            <NuxtLink to="/">Home</NuxtLink>
          </li>
          <li class="slide-menu__content pointer" @click="logout">Logout</li>
          <li class="slide-menu__content" @click="closeMenu">
            <NuxtLink to="/mypage">Mypage</NuxtLink>
          </li>
          <li class="slide-menu__content" @click="closeMenu" v-if="$auth.user.authority ==='admin'">
            <NuxtLink to="/admin">Admin</NuxtLink>
          </li>
          <li class="slide-menu__content" @click="closeMenu" v-if="$auth.user.authority ==='admin' || $auth.user.authority === 'manager'">
            <NuxtLink to="/manager">Manager</NuxtLink>
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
    computed : {
      path() {
        if (this.$route.path === '/admin') {
          return 'theme--admin';
        }
        if (this.$route.path === '/manager') {
          return 'theme--manager'
        }
      }
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
            this.closeMenu();
            await this.$auth.logout();
            this.$router.push("/");
          } catch (error) {
            alert(error);
          }
        }
      },
    },
  };

</script>

<style>
  .hamburger-wrap {
    width: fit-content;
  }

  .hamburger {
    display: flex;
    align-items: center;
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
    transition: 1s;
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
    margin-right: 2vw;
    transition: 1s;
  }

  .welcome {
    font-weight: bold;
    font-size: 15px;
    width: 26vw;
    line-height: 1.2;
    padding-top: 5px;
  }

  .slide-menu {
    width: 100vw;
    height: 100vh;
    position: fixed;
    background: whitesmoke;
    padding: 0 5% 0 5%;
    top: 0;
    right: -100%;
    transition: 600ms ease-out;
    z-index: 50;
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
    transition: 1s;
  }

  .slide-menu__content {
    margin: 40px 0;
    text-shadow: 1px 1px 2px rgb(185, 185, 185);
  }

  .pointer {
    cursor: pointer;
  }

  .welcome-enter-active,
  .welcome-leave-active {
    transition: opacity 1s;
  }

  .welcome-enter,
  .welcome-leave-to{
    opacity: 0;
  } 

  .hamburger__logo.theme--admin {
    background: lightseagreen;
    transition: 1s;
  }
  .rese.theme--admin {
    color: lightseagreen;
    transition: 1s;
  }
  .slide-menu__list.theme--admin {
    color: lightseagreen;
    transition: 1s;
  }

  .hamburger__logo.theme--manager {
    background: coral;
    transition: 1s;
  }
  .rese.theme--manager {
    color: coral;
    transition: 1s;
  }
  .slide-menu__list.theme--manager {
    color: coral;
    transition: 1s;
  }
</style>

<template>
  <div class="admin">
    <div class="create-manager flex">
      <div class="create-manager-box">
        <h2 class="create-manager__title">Create Restaurant Manager</h2>
        <validation-observer ref="obs" v-slot="ObserverProps">
          <form @submit.prevent="registerManager" class="create-manager__form">
            <div class="input">
              <validation-provider v-slot="{errors}" rules="required|min:4|alpha_dash|max:191">
                <p><label>
                    <img src="~assets/img/Registration icon 1.svg" class="name-img">
                    <input type="text" v-model="name" placeholder="Username" name="ユーザーネーム">
                  </label></p>
                <div class="error">{{errors[0]}}</div>
              </validation-provider>
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
              class="button create-manager__button">店舗代表者を作成する</button>
          </form>
        </validation-observer>
      </div>
    </div>
    <div class="managements">
      <h2>未承認の店舗代表者申請</h2>
      <div class="management" v-for="management in managements" :key="management.id">
        <table class="management__table">
          <tr>
            <th>店舗</th>
            <td>
              <NuxtLink :to="'/detail/'+management.restaurant.uuid" target="_blank"
                class="management__table__name to-restaurant">{{management.restaurant.name}}</NuxtLink>
              <span>ID：{{management.restaurant.uuid}}</span>
            </td>
          </tr>
          <tr>
            <th>申請者</th>
            <td>
              <span class="management__table__name">{{management.user.name}}</span>
              <span>メール：{{management.user.email}}</span>
            </td>
          </tr>
        </table>
        <div class="approve-destroy-wrap">
          <button class="destroy-management-button" @click="destroyManagement(management)">申請破棄</button>
          <button class="approve-button" @click="approve(management)">承認する</button>
        </div>
      </div>
      <h2>承認済みの店舗代表者</h2>
      <div class="approved management" v-for="management in approved" :key="management.id">
        <table class="management__table">
          <tr>
            <th>店舗</th>
            <td>
              <NuxtLink :to="'/detail/'+management.restaurant.uuid" target="_blank"
                class="management__table__name to-restaurant">{{management.restaurant.name}}</NuxtLink>
              <span>ID：{{management.restaurant.uuid}}</span>
            </td>
          </tr>
          <tr>
            <th>申請者</th>
            <td>
              <span class="management__table__name">{{management.user.name}}</span>
              <span>メール：</span><span>{{management.user.email}}</span>
            </td>
          </tr>
        </table>
        <div class="unapprove">
          <p class="approved_at">承認日：{{$dayjs(management.approved_at).format('YYYY/M/D')}}</p>
          <button class="unapprove-button" @click="unapprove(management)">承認を取り消す</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    middleware: 'checkAuthority',
    data() {
      return {
        name: null,
        email: null,
        password: null,
        managements: null,
        approved: null,
      };
    },
    methods: {
      async registerManager() {
        try {
          const profile = {
            name: this.name,
            email: this.email,
            password: this.password,
          };
          await this.$axios.post("/v1/user", profile);
          alert('店舗代表者が作成されました。')
        } catch (err) {
          console.log(err);
          alert('このメールアドレスは使用済みか、その他のエラーです。\n詳細：' + err.message);
        }
      },
      async getManagements() {
        const gotData = await this.$axios.get('/v1/management');
        this.managements = gotData.data.managements.filter(mng => !mng.approved_at);
        this.approved = gotData.data.managements.filter(mng => mng.approved_at).sort((a, b) => (a.approved_at > b
          .approved_at) ? -1 : 1);
      },
      async approve(management) {
        const boo = confirm('承認しますか？');
        if (!boo) {
          return;
        }
        try {
          const approval = {
            restaurant_uuid: management.restaurant.uuid,
            user_uuid: management.user.uuid,
          }
          const response = await this.$axios.post('/v1/management/approval', approval);
          const approved = response.data.management;
          const index = this.managements.findIndex(mng => mng.id === approved.id);
          this.managements.splice(index, 1);
          this.approved.unshift(approved);
        } catch (error) {
          alert(error);
        }
      },
      async unapprove(management) {
        const boo = confirm('承認を取り消しますか？');
        if (!boo) {
          return;
        }
        try {
          const approval = {
            restaurant_uuid: management.restaurant.uuid,
            user_uuid: management.user.uuid,
          }
          const response = await this.$axios.post('/v1/management/unapproval', approval);
          const unapproved = response.data.management;
          const index = this.approved.findIndex(mng => mng.id === unapproved.id);
          this.approved.splice(index, 1);
          this.managements.unshift(unapproved);
        } catch (error) {
          alert(error);
        }
      },
      async destroyManagement(management) {
        const boo = confirm('申請を破棄しますか？');
        if (!boo) {
          return;
        }
        try {
          const response = await this.$axios.delete(`/v1/management/${management.id}`);
          const deleted = response.data.management
          const index = this.managements.findIndex(mng => mng.id === deleted.id);
          this.managements.splice(index, 1);
        } catch (error) {
          alert(error)
        }
      }
    },
    created() {
      this.getManagements();
    },
  }

</script>


<style>
  .create-manager {
    margin-top: 60px;
    margin-bottom: 100px;
  }

  .create-manager-box {
    background: white;
    border-radius: 7px;
    box-shadow: 2px 2px 7px gray;
    width: 400px;
  }

  .create-manager__title {
    background: lightseagreen;
    color: white;
    font-size: 18px;
    padding: 18px 20px;
    border-radius: 7px 7px 0 0;
    letter-spacing: 1px;
  }

  .create-manager__form {
    padding: 20px 30px;
  }

  .create-manager__form label {
    display: flex;
    justify-content: space-between;
  }

  .name-img,
  .email-img,
  .lock-img {
    width: 22px;
    position: relative;
  }

  .create-manager__form input {
    border: none;
    border-bottom: gray 1px solid;
    font-size: 15px;
    width: 90%;
  }

  .create-manager__form p {
    padding: 5px 0;
  }

  .create-manager__button {
    background: lightseagreen;
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

  .management {
    background: white;
    margin-bottom: 10px;
    box-shadow: 2px 2px 5px gray;
    border-radius: 5px;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .management__table {
    text-align: left;
  }

  .management__table * {
    padding: 5px;
  }

  .management__table th {
    font-weight: normal;
    border-right: gainsboro 1px solid;
    padding-right: 10px;
  }

  .management__table__name {
    font-weight: bold;
  }

  .approve-button {
    background: coral;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 20px;
    font-size: 13px;
    box-shadow: 1px 1px 3px gray;
    margin-top: 10px;
  }

  .unapprove-button {
    background: crimson;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 5px 20px;
    font-size: 13px;
    box-shadow: 1px 1px 3px gray;
    margin-right: 10px;
  }

  .to-restaurant {
    text-decoration-line: underline;
    text-underline-offset: 2px;
    color: royalblue;
  }

  .managements h2 {
    margin-top: 50px;
    margin-bottom: 15px;
    font-size: 18px;
  }

  .approved_at {
    font-size: 13px;
    text-align: center;
    margin-right: 10px;
    margin-bottom: 8px;
  }

  .destroy-management-button {
    background: gray;
    color: white;
    border: none;
    border-radius: 3px;
    padding: 3px 10px;
    font-size: 11px;
    box-shadow: 1px 1px 2px gray;
  }

  .approve-destroy-wrap {
    display: flex;
    flex-flow: column;
    align-items: flex-end;
    margin-right: 10px;
  }

  @media screen and (max-width: 768px) {
    .management {
      flex-wrap: wrap;
    }

    .approve-destroy-wrap {
      flex-flow: row;
      justify-content: space-evenly;
      width: 100%;
    }

    .management__table__name {
      display: block;
      font-size: 16px !important;
    }

    .unapprove {
      display: flex;
      align-items: baseline;
      justify-content: space-evenly;
      width: 100%;
      margin-top: 10px;
    }

    .management__table span {
      font-size: 13px;
    }
  }

</style>

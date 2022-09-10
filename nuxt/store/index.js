export const mutations = {
  changeEmailVerifiedAt(state, dateTime) {
    state.auth.user.email_verified_at = dateTime;
  },
};
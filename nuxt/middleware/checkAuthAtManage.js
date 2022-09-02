export default async function ({ store, redirect, route }) {
  if (!store.$auth.loggedIn || store.$auth.user.authority === 'user' || store.$auth.user.authority === 'admin') {
    return redirect('/404');
  }
  const sendData = {
    restaurant_uuid: route.params.restaurant_uuid
  };
  try {
    const response = await store.$axios.post('/v1/management/check', sendData);
    if (response.status !== 200) {
      alert('この店舗の代表者ではありません。')
      return redirect('/manager')
    }
    
  } catch (error) {
    return redirect('/404')
  }
}
// ここはcheckAuthorityと挙動を比較するためにあえてtry catchを使う。
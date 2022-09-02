export default async function ({ store, redirect, route }) {
  if (!store.$auth.loggedIn || store.$auth.user.authority === 'user') {
    return redirect('/404');
    // /adminというページは無かったことにするために、ホームにリダイレクトせず、あえて404ページを表示する。/adminと/manageだけ/404にパスが変わるので勘がいい人にはばれる。
  }
  // storeは改ざんされてしまうので、以下でapi側に権限の取得を任せる。上で一度userか見たのはapiへのトラフィックを減らすため。
  // userに対してはlaravelから403や404を投げるとコンソールにapiリクエストのURLが表示されてしまって良くないので204を返し、nuxtではtry catchを使わずに処理する。(ネットワークでも見れちゃうけど。)
  // ブラウザのコンソールにエラーが表示されないならlaravelから404か403を返したい。できそうな感じはするが工数的にペンディング。
  const response = await store.$axios.get('auth/admin', { headers: { Authorization: store.$auth.$storage._state['_token.laravelJWT'] } });
  switch (route.path) {
    case '/admin':
      if (response.status === 204) {
        return redirect('/404');
      } else if (response.data.authority === 'manager') {
        alert('権限がありません。');
        return redirect('/');
      }
    break;

    case '/manager':
      if (response.status === 204) {
        return redirect('/404');
      } 
    break;
  }
}
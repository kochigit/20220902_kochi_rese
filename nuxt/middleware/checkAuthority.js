export default function ({ store, redirect, route }) {
  const auth = store.state.auth.user.authority
  switch (route.path) {
    case '/admin':
      if (auth !== 'admin') {
        alert('権限がありません。');
        return redirect('/');
      }
      break;
  
    case '/manage':
      // auth !== 'user'ではセキュリティ的に不安なので以下の条件文にした
        if (auth !== 'manager' && auth !== 'admin') {
          alert('権限がありません。');
          return redirect('/');
        }
      break;
  }
  
}
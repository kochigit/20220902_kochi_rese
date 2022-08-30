export default function ({ from, redirect }) {
  if (from.name !== 'register') {
    return redirect('/');
  }
}
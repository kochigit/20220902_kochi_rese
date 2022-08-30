export default function ({ from, redirect }) {
  if (from.name !== 'detail-restaurant_uuid') {
    return redirect('/');
  }
}
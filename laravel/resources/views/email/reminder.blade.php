<p>{{$reservation->user->name}}様</p>
<p>Reseでのご予約誠にありがとうございます。</p>
<br>
<p>本日は予約日当日でございます。</p>
<p>予約情報をご確認くださいませ。</p>
<br>
<div class="img-wrap">
  <img src="{{$reservation->restaurant->img_path}}" class="img">
</div>
<p>店舗名：{{$reservation->restaurant->name}}</p>
<p>日付：{{$reservation->date}}</p>
<p>時刻：{{$reservation->time}}</p>
<p>人数：{{$reservation->number}} 名様</p>
<br>
<a href="{{config('app.front_url')}}mypage">マイページで確認</a>

<style>
.img-wrap {
  width: 90vw;
  margin: 0 auto;
}
.img {
  width: 100%;
}
</style>
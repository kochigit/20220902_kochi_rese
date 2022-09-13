<p>{{$user->name}} 様</p>
<p>{{$request->name}}からのお知らせです。</p>
<br>
<div>
  {!! nl2br(e($content)) !!}
</div>
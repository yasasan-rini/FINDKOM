@php $idx = $l->timelineIndex; @endphp
<div class="tl"><div class="track"><i style="width:{{ $idx / 4 * 100 }}%"></i></div>
  @foreach (\App\Support\ReportStatus::STEPS as $i => $s)
    <div class="step {{ $i < $idx ? 'done' : ($i === $idx ? 'cur' : '') }}">{{ $s }}</div>
  @endforeach
</div>
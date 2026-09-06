@props(['status'])
@php($value = $status instanceof \App\Enums\BookingStatus ? $status->value : $status)
<span class="status status-{{ $value }}"><span class="status-dot"></span>{{ str($value)->replace('_',' ')->title() }}</span>

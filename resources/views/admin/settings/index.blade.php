@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    @foreach($settings as $group => $items)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold text-capitalize">{{ $group }} Settings</div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($items as $setting)
                <div class="col-md-6">
                    <label class="form-label">{{ str_replace('_', ' ', ucfirst($setting->key)) }}</label>
                    @if($setting->type === 'boolean')
                        <div class="form-check form-switch">
                            <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                            <input class="form-check-input" type="checkbox" name="settings[{{ $setting->key }}]" value="1"
                                   {{ $setting->value == '1' || $setting->value === true ? 'checked' : '' }}>
                        </div>
                    @elseif($setting->type === 'integer')
                        <input type="number" name="settings[{{ $setting->key }}]" class="form-control"
                               value="{{ $setting->value }}">
                    @else
                        <input type="text" name="settings[{{ $setting->key }}]" class="form-control"
                               value="{{ $setting->value }}">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Save Settings
    </button>
</form>
@endsection

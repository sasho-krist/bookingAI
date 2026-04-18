@props(['inputIdPrefix' => 'day'])

{{-- Полета за графика; изисква променливи $initialDays и $dayLabels в родителя --}}
@error('days')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead>
            <tr>
                <th scope="col">Ден</th>
                <th scope="col">Работи</th>
                <th scope="col">От</th>
                <th scope="col">До</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dayLabels as $key => $label)
                @php
                    $st = $initialDays[$key] ?? ['active' => false, 'open' => '09:00', 'close' => '18:00'];
                    $active = old('days.'.$key.'.active', $st['active'] ? '1' : '0');
                    $isActive = $active === '1' || $active === 1 || $active === true;
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $label }}</td>
                    <td>
                        <input type="hidden" name="days[{{ $key }}][active]" value="0">
                        <div class="form-check mb-0">
                            <input type="checkbox" name="days[{{ $key }}][active]" value="1" class="form-check-input" id="{{ $inputIdPrefix }}_{{ $key }}_active" @checked($isActive)>
                            <label class="form-check-label visually-hidden" for="{{ $inputIdPrefix }}_{{ $key }}_active">Отворено</label>
                        </div>
                    </td>
                    <td>
                        <input type="time" name="days[{{ $key }}][open]" id="{{ $inputIdPrefix }}_{{ $key }}_open" value="{{ old('days.'.$key.'.open', $st['open']) }}" class="form-control form-control-sm @error('days.'.$key.'.open') is-invalid @enderror" step="300">
                        @error('days.'.$key.'.open')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                    <td>
                        <input type="time" name="days[{{ $key }}][close]" id="{{ $inputIdPrefix }}_{{ $key }}_close" value="{{ old('days.'.$key.'.close', $st['close']) }}" class="form-control form-control-sm @error('days.'.$key.'.close') is-invalid @enderror" step="300">
                        @error('days.'.$key.'.close')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

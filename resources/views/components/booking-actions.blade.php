@props(['booking'])

@if ($booking->status === 'pending')
    <div class="btn-group btn-group-sm" role="group" aria-label="Действия по резервация">
        <form method="POST" action="{{ route('bookings.approve', $booking) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-success">Одобри</button>
        </form>
        <form method="POST" action="{{ route('bookings.reject', $booking) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-danger">Откажи</button>
        </form>
    </div>
@endif

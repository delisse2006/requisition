@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h3 class="mb-0">Notifications</h3>
		<div class="d-flex gap-2">
			<form method="POST" action="{{ route('notifications.markAllAsRead') }}">
				@csrf
				<button class="btn btn-sm btn-outline-primary">Mark all as read</button>
			</form>
			<form method="POST" action="{{ route('notifications.destroyAll') }}" onsubmit="return confirm('Delete all notifications?');">
				@csrf
				@method('DELETE')
				<button class="btn btn-sm btn-outline-danger">Delete all</button>
			</form>
		</div>
	</div>

	@if($notifications->isEmpty())
		<div class="text-center py-5 text-muted">No notifications found.</div>
	@else
		<div class="list-group">
			@foreach($notifications as $n)
				<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start {{ $n->read ? '' : 'list-group-item-primary' }}">
					<div class="ms-2 me-auto">
						<div class="fw-bold">{!! '<i class="' . $n->icon . ' me-2"></i>' !!} {{ $n->title }}</div>
						<div class="small text-muted">{!! nl2br(e($n->message)) !!}</div>
						<div class="small text-muted mt-1">{{ $n->created_at_human }}</div>
					</div>
					<div class="btn-group btn-group-sm">
						@if(!$n->read)
							<form method="POST" action="{{ route('notifications.markAsRead', $n) }}">
								@csrf
								<button class="btn btn-sm btn-success">Mark read</button>
							</form>
						@endif
						<form method="POST" action="{{ route('notifications.destroy', $n) }}" onsubmit="return confirm('Delete this notification?');">
							@csrf
							@method('DELETE')
							<button class="btn btn-sm btn-outline-danger">Delete</button>
						</form>
					</div>
				</div>
			@endforeach
		</div>

		<div class="mt-3">{{ $notifications->links() }}</div>
	@endif
</div>
@endsection

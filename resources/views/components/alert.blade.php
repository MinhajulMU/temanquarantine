@if(session()->has('status'))

	@if(session('status') == true)
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong><i class="mdi mdi-check-circle"></i> </strong> {!! session()->get('message') !!}
	</div>
	@else
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<strong><i class="mdi mdi-close-circle"></i> </strong> {!! session()->get('message') !!}
	</div>
	@endif

@endif
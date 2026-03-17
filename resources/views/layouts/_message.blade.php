@if (session('success'))
<div class="alert alert-success alert-dismissible toast-alert" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    {{ session('success') }}
</div>
@endif
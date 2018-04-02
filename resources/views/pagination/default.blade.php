@if ($paginator->lastPage() > 1)
<div class="page page-right">
    {!! $paginator->appends(Input::except('page'))->render() !!}
    <span class="page-few">第{!! $paginator->currentPage() !!}页，每页{!! $paginator->perPage() !!}条，共{!! $paginator->total() !!}条</span>
</div>
@endif

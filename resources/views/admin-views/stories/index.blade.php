@extends('layouts.admin.app')

@section('title', __('Stories'))

@section('content')
<div class="content container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h2 class="page-header-title mb-0">{{ __('Stories') }}</h2>
            <p class="mb-0 text-muted">{{ __('Manage restaurant stories and visibility.') }}</p>
        </div>
    </div>

    <form action="" method="get" class="mb-3">
        <div class="card p-3">
            <div class="row g-3 align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label class="form-label">{{ translate('messages.status') }}</label>
                    <select name="status" class="form-control">
                        <option value="">{{ translate('messages.all') }}</option>
                        @foreach ([
                            \App\Models\Story::STATUS_DRAFT => __('Draft'),
                            \App\Models\Story::STATUS_SCHEDULED => __('Scheduled'),
                            \App\Models\Story::STATUS_PUBLISHED => __('Published'),
                            \App\Models\Story::STATUS_EXPIRED => __('Expired'),
                        ] as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-3">
                    <label class="form-label">{{ translate('messages.restaurant') }}</label>
                    <select name="restaurant_id" class="form-control">
                        <option value="">{{ translate('messages.all') }}</option>
                        @foreach ($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}" {{ (string) request('restaurant_id') === (string) $restaurant->id ? 'selected' : '' }}>
                                {{ $restaurant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ translate('messages.filter') }}</button>
                </div>
                <div class="col-sm-4 col-md-2">
                    <a href="{{ route('admin.stories.index') }}" class="btn btn-secondary w-100">{{ translate('messages.reset') }}</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('messages.id') }}</th>
                        <th>{{ translate('messages.restaurant') }}</th>
                        <th>{{ translate('messages.title') }}</th>
                        <th>{{ translate('messages.status') }}</th>
                        <th>{{ __('Publish time') }}</th>
                        <th>{{ __('Expire time') }}</th>
                        <th>{{ __('Views') }}</th>
                        <th>{{ __('Completed views') }}</th>
                        <th>{{ translate('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stories as $story)
                        <tr>
                            <td>{{ $story->id }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $story->restaurant?->name ?? translate('messages.not_found') }}</span>
                                    <small class="text-muted">
                                        {{ $story->restaurant?->stories_enabled ? __('Stories enabled') : __('Stories disabled') }}
                                    </small>
                                </div>
                            </td>
                            <td>{{ $story->title ?? __('Untitled') }}</td>
                            <td><span class="badge bg-light text-dark text-capitalize">{{ $story->status }}</span></td>
                            <td>{{ optional($story->publish_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ optional($story->expire_at)->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>{{ $story->view_count ?? 0 }}</td>
                            <td>{{ $story->completed_view_count ?? 0 }}</td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    @if ($story->restaurant)
                                        <form action="{{ route('admin.stories.restaurant.toggle', $story->restaurant_id) }}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                {{ $story->restaurant->stories_enabled ? __('Disable stories') : __('Enable stories') }}
                                            </button>
                                        </form>
                                    @endif

                                    @if (in_array($story->status, [\App\Models\Story::STATUS_PUBLISHED, \App\Models\Story::STATUS_SCHEDULED]))
                                        <form action="{{ route('admin.stories.expire', $story->id) }}" method="post"
                                            onsubmit="return confirm('{{ __('Expire this story now?') }}');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">{{ __('Expire now') }}</button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.stories.destroy', $story->id) }}" method="post"
                                        onsubmit="return confirm('{{ __('Are you sure you want to delete this story?') }}');">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-danger">{{ translate('messages.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">{{ translate('messages.no_data_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {!! $stories->links() !!}
        </div>
    </div>
</div>
@endsection

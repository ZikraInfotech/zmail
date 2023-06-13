<div class="page-title">
    <div class="d-flex mt-4">
        <div>
            <h1 class="d-flex align-items-center">
                <span class="text-semibold mr-3">{{ $campaign->name }}</span>
                <span class="d-flex" title='{{ $campaign->status == Acelle\Model\Campaign::STATUS_ERROR ? $campaign->last_error : '' }}' data-popup='tooltip'>
                    <span class="label label-flat bg-{{ $campaign->status }}">{{ trans('messages.campaign_status_' . $campaign->status) }}</span>
                </span>
            </h1>
        </div>
        <div class="ms-auto">
        </div>
    </div>
        
</div>

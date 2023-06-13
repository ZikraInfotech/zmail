@extends('layouts.popup.large')

@section('bar-title')
    {{ trans('messages.subscriber_import') }}
@endsection

@section('content')
	<div class="popup-wizard">
        @include('subscribers.import2._sidebar', ['step' => 'review'])
        
        <div class="wizard-content">
            <p>{!! trans('messages.subscriber.import.running.wording', [
                'link' => url('files/csv_import_example.csv')
            ]) !!}</p>   

            <div id="ImportProgressContent">

            </div>

            <div>
                <a id="ImportCancelButton" href="javascript:;" class="btn btn-secondary me-1" progress-control="cancel">
                    {{ trans('messages.cancel') }}
                </a>

                <a id="ImportRetryButton" href="javascript:;" class="btn btn-default me-1" progress-control="retry">
                    <span class="material-symbols-rounded">restart_alt</span> {{ trans('messages.import.retry') }}
                </a>
    
                <a id="ImportCloseButton" href="javascript:;" class="btn btn-default" onclick="window.location.reload()" progress-control="close">
                    {{ trans('messages.import.hide') }}
                </a>
            </div>
        </div>
    </div>

    <script>
        var SubscribersImportProgress = {

            checkUrl: '{{ action('SubscriberController@import2ProgressContent', [
                'list_uid' => $list->uid,
                'job_uid' => $job_uid,
            ]) }}',

            cancelUrl: '{{ action('SubscriberController@cancelImport', [
                'job_uid' => $job_uid,
            ]) }}',

            checkProgress: function() {
                $.ajax({
                    url: SubscribersImportProgress.checkUrl,
                    type: 'GET'
                }).done(function(response) {
                    console.log(response);
                    $('#ImportProgressContent').html(response);

                }).fail(function(jqXHR, textStatus, errorThrown) {
                }).always(function() {
                });
            },

            cancelImport: function() {
                $.ajax({
                    url: SubscribersImportProgress.cancelUrl,
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN
                    }
                }).done(function(response) {
                    window.location.reload();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                }).always(function() {
                });
            },

            retryImport: function() {
                $.ajax({
                    url: SubscribersImportProgress.cancelUrl,
                    type: 'POST',
                    data: {
                        _token: CSRF_TOKEN
                    }
                }).done(function(response) {
                    SubscribersImport2.loadUploadPopup();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                }).always(function() {
                });
            }
        };

        $(function() {
            // check progress
            SubscribersImportProgress.checkProgress();

            // cancel import
            $('#ImportCancelButton').on('mousedown', function() {
                var cancelConfirm = confirm("{{ trans('messages.list.import.cancel') }}");

                if (cancelConfirm) {
                    SubscribersImportProgress.cancelImport();
                }
            });

            // retry import
            $('#ImportRetryButton').on('mousedown', function() {
                SubscribersImportProgress.retryImport();
            });
        })
    </script>
@endsection
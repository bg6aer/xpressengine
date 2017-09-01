<form action="{{ route('settings.plugins.manage.activate') }}" method="post" onsubmit="return ($('.__xe_select-plugin:checked').length != 0);">
    {{ csrf_field() }}
    <div class="xe-modal-header">
        <button type="button" class="btn-close" data-dismiss="xe-modal" aria-label="Close"><i class="xi-close"></i></button>
        <strong class="xe-modal-title">{{ xe_trans('xe::plugin') }} {{ xe_trans('xe::activate') }}</strong>
    </div>
    <div class="xe-modal-body">
        <div class="xe-lypop-plugin">
            <p class="xe-lypop-plugin-text">
                아래 플러그인을 활성화하시겠습니까?
            </p>
            <div class="xe-lypop-plugin-check version">

                    @foreach($plugins as $plugin)

                    @if($plugin->isActivated())
                    <label class="xe-label">
                        <input type="checkbox" disabled checked>
                        <span class="xe-input-helper"></span>
                        <div class="xe-label-text"><span>{{ $plugin->getTitle() }}</span><b>({{ $plugin->getId() }})</b> 이미 활성화 되어 있음</div>
                    </label>
                    @else
                    <label class="xe-label">
                        <input type="checkbox" class="__xe_select-plugin" name="pluginId[]" value="{{ $plugin->getId() }}" checked>
                        <span class="xe-input-helper"></span>
                        <div class="xe-label-text"><span>{{ $plugin->getTitle() }}</span><b>({{ $plugin->getId() }})</b></div>
                    </label>
                    @endif
                    @endforeach

            </div>
        </div>
    </div>
    <div class="xe-modal-footer">
        <button type="button" class="xe-btn xe-btn-secondary" data-dismiss="xe-modal">{{ xe_trans('xe::cancel') }}</button>
        <button type="submit" class="xe-btn xe-btn-primary" >{{ xe_trans('xe::activate') }}</button>
    </div>
</form>


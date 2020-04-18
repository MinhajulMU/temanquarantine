<?php
    if(!isset($route_params))
    {
        $route_params = [];
    }
?>

<a href="{{ route(strtolower($module).'.index',$route_params) }}" class="btn btn-primary">
    <i class="mdi mdi-arrow-left"></i> @lang('common.back')
</a>

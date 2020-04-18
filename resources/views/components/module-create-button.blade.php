<?php
    if(!isset($route_params))
    {
        $route_params = [];
    }
?>
{{ MyAuth::allowActionButton(strtolower($module).'.create', $route_params, '+ '.__('common.add').' '.$title, ['class' => 'btn btn-primary waves-effect waves-light']) }}
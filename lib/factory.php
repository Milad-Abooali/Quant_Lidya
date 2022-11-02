<?php

/**
 * Factory Class
 * 17:01 AM Friday, Dec 20, 2020 | M.Abooali
 */

class factory {

    function __construct() {

    }

    public static function getCustomDataByID ($id, $costume) {
        $u_manager = new usermanager();
        return $u_manager->getCustom($id, $costume);
    }

    public static function dataTableSimple($row, $query, $options=null, $reload=false, $sm=null){
        $query_http = http_build_query($query);
        $id = ($query['table_html']) ?? 'f-dt-'.mt_rand(999, 99999);
        GF::loadCSS('h','https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.5.4/r-2.2.2/rg-1.1.2/datatables.min.css');
        GF::loadJS('f','https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js');
        GF::loadJS('f','https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.5.4/r-2.2.2/rg-1.1.2/datatables.min.js');
        $id_JS = str_replace('-','_',$id);
        $js = "
              DT_$id_JS = $('#$id').DataTable( {
                'pageLength': $row,
                'processing': true,
                'serverSide': true,
                'responsive': true,
                'deferRender': true,
                'ajax':  {
                  url: 'lib/ajax.php?c=datatable&f=dataTableSimple&$query_http&t=".TOKEN."',
                  type: 'POST',
                  data: {CustomOperation: function() { let data = {};$( '.DT_".$id_JS."_CustomOperation' ).each(function( index ) {data[index] = $( this ).val();}); return JSON.stringify(data)}}
                }
            ";
        if ($options) $js .= ','.$options;
        $js .= "});";
        GF::makeJS('f',"var DT_$id_JS;",false);
        GF::makeJS('f',$js);
        $table = '<table id="'.$id.'" class="table-DT table table-striped table-hover '.$sm.'"  style="width: 100%; "><thead><tr>';
        foreach ($query['columns'] as $column) $table .= '<th>'.$column['th'].'</th>';
        $table .='</tr></thead></table>';
        return $table;
    }

    public static function dataTableComplex($row, $query, $options=null, $reload=false, $sm=null){
        $query_http = http_build_query($query);
        $id = ($query['table_html']) ?? 'f-dt-'.mt_rand(999, 99999);
        GF::loadCSS('h','https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.5.4/r-2.2.2/rg-1.1.2/datatables.min.css');
        GF::loadJS('f','https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js');
        GF::loadJS('f','https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.5.4/r-2.2.2/rg-1.1.2/datatables.min.js');
        $id_JS = str_replace('-','_',$id);
        $js = "
              DT_$id_JS = $('#$id').DataTable( {
                'pageLength': $row,
                'processing': true,
                'serverSide': true,
                'responsive': true,
                'deferRender': true,
                'ajax':  {
                  url: 'lib/ajax.php?c=datatable&f=dataTableComplex&$query_http&t=".TOKEN."',
                  type: 'POST',
                  data: {
                  CustomOperation: function() { let data = {};$( '.DT_".$id_JS."_CustomOperation' ).each(function( index ) {data[index] = $( this ).val();}); return JSON.stringify(data)},
                  GroupBy:'".$query['group_by']."'
                  }
                }
            ";
        if ($options) $js .= ','.$options;
        $js .= "});";
        GF::makeJS('f',"var DT_$id_JS;",false);
        GF::makeJS('f',$js);
        $table = '<table id="'.$id.'" class="table-DT table table-striped table-hover '.$sm.'" style="width: 100%; "><thead><tr>';
        foreach ($query['columns'] as $column) $table .= '<th>'.($column['th'] ?? $column['db']).'</th>';
        $table .='</tr></thead></table>';
        return $table;
    }

    public static function dataTableUnion($row, $query, $options=null, $reload=false, $sm=null){
        $query_http = http_build_query($query);
        $id = ($query['table_html']) ?? 'f-dt-'.mt_rand(999, 99999);
        GF::loadCSS('h','https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.5.4/r-2.2.2/rg-1.1.2/datatables.min.css');
        GF::loadJS('f','https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js');
        GF::loadJS('f','https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.21/b-1.5.4/r-2.2.2/rg-1.1.2/datatables.min.js');
        $id_JS = str_replace('-','_',$id);
        $js = "
              DT_$id_JS = $('#$id').DataTable( {
                'pageLength': $row,
                'processing': true,
                'serverSide': true,
                'responsive': true,
                'deferRender': true,
                'ajax':  {
                  url: 'lib/ajax.php?c=datatable&f=dataTableUnion&$query_http&t=".TOKEN."',
                  type: 'POST',
                  data: {CustomOperation: function() { let data = {};$( '.DT_".$id_JS."_CustomOperation' ).each(function( index ) {data[index] = $( this ).val();}); return JSON.stringify(data)}}
                }
            ";
        if ($options) $js .= ','.$options;
        $js .= "});";
        GF::makeJS('f',"var DT_$id_JS;",false);
        GF::makeJS('f',$js);
        $table = '<table id="'.$id.'" class="table-DT table table-striped table-hover '.$sm.'" style="width: 100%; "><thead><tr>';
        foreach ($query['columns'] as $column) $table .= '<th>'.($column['th'] ?? $column['db']).'</th>';
        $table .='</tr></thead></table>';
        return $table;
    }

    public static function widget($name, $title=null, $size='6',$autoload=false, $vars='{}',$group=false,$close=1,$reload=1,$date=1) {
        static $widget_number = 0;
        ++$widget_number;
        $group = ($group) ? 'data-widget-group="'.$group.'"' : null;
        $close = ($close) ? '<i class="hidewg pl-2 fa fa-window-close fa-1x pt-1 text-danger" data-toggle="tooltip" data-placement="top"  data-original-title="Close Widget"></i>' : null;
        $reload = ($reload) ? '<i class="reload fas fa-sync fa-spin" data-toggle="tooltip" data-placement="top"  data-original-title="Reload Widget"></i>' : null;
        $date = ($date) ? '<span class="datetime text-black-50" data-toggle="tooltip" data-placement="top"  data-original-title="Updated Time"></span>' : null;
        if($date && ($close || $reload)) $date .= '<span class="px-2">|</span>';
        echo '<div class="col-xl-'.$size.' widget-start">
                <div class="card pmd-card">
                    <div class="card-body">
                        <div id="wg-'.$name.'-'.$widget_number.'" data-autoload="'.$autoload.'" data-wg="'.$name.'.php"  data-vars='.$vars.' '.$group.' class="widget row">
                            <div class="widget-header col-12 d-flex justify-content-between">
                                <h6 class="mt-0 m-b-30 header-title float-left">'.$title.'</h6>
                                <span class="float-right">
                                    '.$date.$reload.$close.'
                                </span>
                            </div>
                            <div class="widget-body col-12"></div>
                        </div>
                    </div>
                </div>
            </div>';
    }

    public static function header() {
        global $_sys_header;
        return $_sys_header;
    }

    public static function footer() {
        global $_sys_footer;
        GF::loadJS('f','assets/js/final-fix.js',true);
        return $_sys_footer;
    }

}


###### TEST PAD

<?php 
$currentPage = $paginator->getCurrentPage(); 
$lastPage = $paginator->getLastPage();
$url = $paginator->getUrl(1);
$url = substr( $url, 0, strrpos( $url, "?"));
$routeName = end((explode('/', $url)));
$Model = str_replace('-', '', Str::studly($routeName));
$indexFields = $Model::structure()['fields'];
$Controller = $Model.'Controller';
$suffixes = $Controller::getSuffixes();
?>

<script type="text/javascript">
$(document).ready(function(){

    function getQueryVariable(variable)
    {
     var query = window.location.search.substring(1);
     var vars = query.split("&");
     for (var i=0;i<vars.length;i++) {
         var pair = vars[i].split("=");
         if(pair[0] == variable){return pair[1];}
     }
     return('false');
 }

 var options = {
    bootstrapMajorVersion: 3,
    currentPage: {{ $currentPage }},
    totalPages: {{ $lastPage }},
    onPageClicked: function(e,originalEvent,type,page){

        var result = { page : page, view : 'paginate' };

        @foreach($indexFields as $field => $structure)
        @foreach($suffixes as $suffix => $operator)
        var value = getQueryVariable('{{ $field.$suffix }}');
        var url_parameter = { '{{ $field.$suffix }}' : value };
        if(value != 'false' && value != '')
        {
            $.extend(result, url_parameter);
        }        
        @endforeach
        @endforeach

        $.ajax({
            url  : "{{ $url }}",
            type : "get",
            data : result,
            success : function(response){
                if(response){
                    $("#index_table").find('tbody').children().remove();
                    $("#index_table").find('tbody').append(response);
                }
            }
        });
    }
}

$('#pagination_index').bootstrapPaginator(options);
});
</script>
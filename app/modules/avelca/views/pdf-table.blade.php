<!DOCTYPE html>
<html>
<head>
    <title>{{$title}}</title>
    <style type="text/css">
        th, td {
            border: 1px solid;
            padding: 0.5rem;
            text-align: left;
        }
        table {
          border-collapse: collapse;
        }
        thead tr{
            background:#DCDEDD;
        }
    </style>
</head>
<body>
    <?php $customView = 'admin.'.$routeName.'.additional.pdf-before-'.$table; ?>
    @if(View::exists($customView))
    @include($customView)
    @endif
    <?php $no = 1; ?>
    <table class="datatable table table-striped table-bordered" >
        <thead>
          <tr>
            <th>No</th>
            @foreach($indexFields as $field => $structure)
            @if( $structure['type'] != 'fk')
            <th class="text-center">{{ AvelcaController::tableHeader($field, $structure) }}</th>
            @endif
            @endforeach
          </tr>
        </thead>
        <tbody>
            @foreach($records as $record)
                <tr>
                  <td><?php echo $no;?></td>
                    @foreach($indexFields as $field => $structure)
                    @if( $structure['type'] != 'fk')
                    <td class="text-center">
                      {{ AvelcaController::viewIndexContent($record, $structure, $field) }}    
                    </td>
                    @endif
                    @endforeach
                  <?php $no++; ?>
                </tr>
            @endforeach
        </tbody>
    </table>
    <?php $customView = 'admin.'.$routeName.'.additional.pdf-after-'.$table; ?>
    @if(View::exists($customView))
    @include($customView)
    @endif
</body>
</html>
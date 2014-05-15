<div class="table-responsive">
    <table class="table table-striped">
    <thead>
        <tr>
        @foreach ($cols as $col)
        <th>{{{$col}}}</th>
        @end
        @if (!empty($links))
        <th></th>
        @end
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
          <tr>
            @foreach ($row as $key => $data)
                @if ($key !== '__id')
                    <td>{{{$data}}}</td>
                @end
            @end
            @if (!empty($links))
                @foreach ($links as $text => $link)
                    <td><a href="{{str_replace('{id}', $row['__id'], $link)}}" class="btn btn-success">{{$text}}</a></td>
                @end
            @end
          </tr>
        @end
    </tbody>
    </table>

    <ul class="pagination">
        <li>
        @if ($page > 1)
            <a href="{{$url}}page=1">&laquo;</a>
        @else
            <a>&laquo;</a>
        @end
        </li>
        @foreach ($pages as $p) 
            @if ($p == $page)
                <li><a>{{$p}}</a></li>
            @else
                <li><a href="{{$url}}page={{$p}}">{{$p}}</a></li>
            @end
        @end
        <li>
        @if ($tpages != $page)
            <a href="{{$url}}page={{$tpages}}">&raquo;</a>
        @else
            <a>&raquo;</a>
        @end
        </li>
    </ul>
</div>


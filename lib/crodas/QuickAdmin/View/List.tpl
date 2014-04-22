<div class="table-responsive">
    <table class="table table-striped">
    <thead>
        <tr>
        @foreach ($cols as $col)
        <th>{{{$col}}}</th>
        @end
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
          <tr>
            @foreach ($row as $data)
            <td>{{{$data}}}</td>
            @end
          </tr>
        @end
    </tbody>
    </table>

    <ul class="pagination">
        <li>
        @if ($page > 1)
            <a href="{{$url}}page={{$page-1}}">&laquo;</a>
        @else
            &laquo;
        @end
        </li>
        @foreach ($pages as $p) 
            @if ($p == $page)
                <li>{{$p}}</li>
            @else
                <li><a href="{{$url}}page={{$p}}">{{$p}}</a></li>
            @end
        @end
        <li>
        @if (count($pages) > $page)
            <a href="{{$url}}page={{$page+1}}">&raquo;</a>
        @else
            &raquo;
        @end
        </li>
    </ul>
</div>


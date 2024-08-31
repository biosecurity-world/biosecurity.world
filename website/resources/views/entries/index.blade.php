<!DOCTYPE html>
<html lang="en">
<body>
@foreach($links as $link)
    <a href="{{$link}}">{{$link}}</a>
@endforeach
</body>
</html>

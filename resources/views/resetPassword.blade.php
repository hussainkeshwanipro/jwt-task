<form method="post" action="{{route('submitPassword')}}">
    @csrf

    New Password <br>
    <input type="password" class="form-control" name="password">

    <input type="hidden" name="token" value="{{$token}}">
    <br>
    To Reset password <input type="submit" value="submit" name="reset">

</form>
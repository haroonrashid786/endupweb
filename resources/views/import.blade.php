<form method="POST" action="{{ route('import.zones.sheet') }}" enctype="multipart/form-data">
@csrf
<input type="file" name="file"/>
<input type="submit" value="Submit">
</form>

<link rel="stylesheet" href="{{ asset('/css/app.css') }}">
<div class="row row-cols-4 row-cols-sm-4 row-cols-md-4 row-cols-lg-4 row-cols-xl-4">
  @foreach ($showImage as $key => $image)
    <div class="card" style="width:10rem;margin:20px;border:none;">
        <div class="container">
        <img class="card-img-top img-produktifitas" src="{{asset('imageUpload/'. $image->name)}}" alt="Card image cap">
          <div class="text-center card-body card-body-image-upload">
            <form action="{{route('deleteImage', $image->id)}}" method="POST">
              @csrf
              <button class=" btn btn-danger btn-image-produktivitass" type="submit"> delete </button>
            </form>
          </div>
      </div>
    </div>
    @endforeach
</div>
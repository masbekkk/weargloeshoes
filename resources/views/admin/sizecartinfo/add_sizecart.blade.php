@extends('admin.index')

@section('data')
<div class="container">
  <h1>size_cart</h1>
  <form action="/admin/sizecartinfo/create_sizecart" method="post" enctype="multipart/form-data">
      @csrf
      <h1>size</h1>
      <input class="form-control" type="text" placeholder="Default input" name="size" aria-label="default input example">
      <h1>foot_length</h1>
      <input class="form-control" type="text" placeholder="Default input" name="foot_length" aria-label="default input example">
      <h1>active</h1>            
      <select class="form-control" id="active" name="active">
      <option value="1">yes</option>
      <option value="0">no</option>
      </select>
      <button type="submit" name="size_cart" class="btn btn-primary mt-3">Submit</button>
  </form>
</div>
@endSection
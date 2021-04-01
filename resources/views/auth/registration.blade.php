{{--@extends('layouts.master')--}}

{{--@section('content')--}}
    <h2>Registro</h2>
    <form method="POST" action="#">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" class="form-control" id="name" name="name">
        </div>

        <div class="form-group">
            <label for="surname">Apellidos:</label>
            <input type="text" class="form-control" id="surname" name="surname">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <div class="form-group">
            <button style="cursor:pointer" type="submit" class="btn btn-primary">Submit</button>
        </div>
        {{--@include('partials.formerrors')--}}
    </form>
{{--@endsection--}}

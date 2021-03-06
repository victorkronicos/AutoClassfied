@extends('admin.dashboard')

@section('subcontent')
    <section class="content-header">
        <h1>
            Marca
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('marcas.show_fields')
                    <a href="{!! route('marcas.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection

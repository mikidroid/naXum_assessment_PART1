@extends('layouts.app')

@section('content')
    <div class="vh-100">
      <div class="contain">
        <table class="table">
            <thead>
                <tr class="table-primary1">

                    <th scope="col">Top</th>
                    <th scope="col">Distributor Name</th>
                    <th scope="col">Total Sales</th>

                </tr>
            </thead>
            <tbody>

                @foreach ($ranked as $i)
                    <tr>

                        <th scope="row">{{ $i->position }}</th>
                        <td>{{ $i->first_name }} {{ $i->last_name }}</td>
                        <td>{{ $i->sales }}</td>

                    </tr>
                @endforeach

            </tbody>
        </table>
      </div>
    </div>

    <style>
    .table-primary1{
       color: white;
       background-image: linear-gradient(to right,#acacef,blue);
     }
    @media (min-width:576px){
     .contain{
       padding: 40px 70px;
     }
      
    }
    @media (max-width:576px){
     .contain{
       padding:20px 20px;
     }
     table{
       overflow:auto;
     }
  
    }    
    </style>
@endsection

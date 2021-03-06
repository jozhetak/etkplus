@extends('layouts.dashboard.master')

@section('description')
@endsection
@section('keywords')
@endsection
@section('title')
Операции
@endsection
@section('content')
<div class="wrapper">
    @include('includes.dashboard.sidebar')
    <div class="main-panel">
        @include('includes.dashboard.top_nav')
        <div class="content">
            <div class="container-fluid">
                @include('includes/notifications')
                <div class="row">
                 <div class="col-md-12">
                    <a class="btn btn-danger btn-fill btn-wd btn-square" href="{{ route('dashboard.partner.create-operation.get') }}">Добавить операцию</a>
                </div>

            </div>
            <br>
            <div class="row">
             <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Операции</h4>
                    </div>
                    <div class="card-content table-responsive table-full-width">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Карта</th>
                                    <th class="text-right">Счет</th>
                                    <th class="text-right">Скидка (%)</th>
                                    <th class="text-right">Скидка (р)</th>
                                    <th class="text-right">Бонус (р)</th>
                                    <th class="text-right">Оператор</th>
                                    <th class="text-right">Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($operations as $operation)
                                <tr>
                                    <td class="text-center">{{ $operation->id }}</td>
                                    <td><a href="{{ route('dashboard.partner.show-card.get', ['card_number' => $operation->card_number]) }}">{{ $operation->card_number }}</a></td>
                                    <td class="text-right">{{ $operation->bill }}</td>
                                    <td class="text-right">{{ $operation->discount }}</td>
                                    <td class="text-right">{{ $operation->discount_value }}</td>
                                    <td class="text-right">{{ $operation->bonus }}</td>
                                    <td class="text-right"><a href="{{ route('dashboard.partner.show-operator.get', ['operator_id' => $operation->operator_id]) }}">{{ $operation->operator }}</a></td>
                                    <td class="text-right">{{ $operation->created_at }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @include('includes.dashboard.footer')
</div>

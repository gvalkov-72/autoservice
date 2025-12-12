@extends('adminlte::page')

@section('title', 'Отчети')

@section('content_header')
    <h1>Отчети</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="small-box bg-info">
                <div class="inner">
                    <h4>Месечен отчет</h4>
                    <p>Приходи и ДДС за избран месец</p>
                </div>
                <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                <a href="{{ route('admin.reports.monthly') }}" class="small-box-footer">Отвори <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
@stop
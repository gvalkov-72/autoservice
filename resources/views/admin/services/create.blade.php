@extends('adminlte::page')

@section('title', 'Нова услуга')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Нова услуга</h1>
        <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Назад
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.services.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Код <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" 
                                   class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code') }}" required>
                            @error('code')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Уникален код за услугата</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Име <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание</label>
                    <textarea name="description" id="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="price">Цена (без ДДС) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="price" id="price" step="0.01" min="0"
                                       class="form-control @error('price') is-invalid @enderror"
                                       value="{{ old('price') }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">лв.</span>
                                </div>
                            </div>
                            @error('price')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="vat_percent">ДДС % <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="vat_percent" id="vat_percent" step="0.01" min="0" max="100"
                                       class="form-control @error('vat_percent') is-invalid @enderror"
                                       value="{{ old('vat_percent', 20) }}" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            @error('vat_percent')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="duration_minutes">Продължителност</label>
                            <div class="input-group">
                                <input type="number" name="duration_minutes" id="duration_minutes" min="1"
                                       class="form-control @error('duration_minutes') is-invalid @enderror"
                                       value="{{ old('duration_minutes') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">мин.</span>
                                </div>
                            </div>
                            @error('duration_minutes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Категория</label>
                            <select name="category_id" id="category_id" 
                                    class="form-control select2 @error('category_id') is-invalid @enderror">
                                <option value="">Без категория</option>
                                @foreach($categories as $id => $name)
                                    <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="is_active">Статус</label>
                            <select name="is_active" id="is_active" 
                                    class="form-control @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', 1) ? 'selected' : '' }}>Активна</option>
                                <option value="0" {{ !old('is_active', 1) ? 'selected' : '' }}>Неактивна</option>
                            </select>
                            @error('is_active')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Бележки</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Запази услуга
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                        Отказ
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap'
        });
    });
</script>
@endpush
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    
                    <h2>По платформам</h2>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Значение</th>
                                <th>Визиты</th>
                                <th>Хосты</th>
                                <th>Куки</th>
                            </tr>
                        </thead>
                    @forelse ($platforms as $name => $val)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $val['visits'] }}</td>
                        <td>{{ $val['hosts'] }}</td>
                        <td>{{ $val['cookies'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">Нет данных</td>
                    </tr>
                    @endforelse
                    </table>
                    
                    <h2>По браузерам</h2>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Значение</th>
                                <th>Визиты</th>
                                <th>Хосты</th>
                                <th>Куки</th>
                            </tr>
                        </thead>
                    @forelse ($browsers as $name => $val)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $val['visits'] }}</td>
                        <td>{{ $val['hosts'] }}</td>
                        <td>{{ $val['cookies'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">Нет данных</td>
                    </tr>
                    @endforelse
                    </table>
                    
                    <h2>По странам</h2>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Значение</th>
                                <th>Визиты</th>
                                <th>Хосты</th>
                                <th>Куки</th>
                            </tr>
                        </thead>
                    @forelse ($countries as $name => $val)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $val['visits'] }}</td>
                        <td>{{ $val['hosts'] }}</td>
                        <td>{{ $val['cookies'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">Нет данных</td>
                    </tr>
                    @endforelse
                    </table>
                    
                    <h2>По реферерам</h2>
                    <table class="table table-bordered table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Значение</th>
                                <th>Визиты</th>
                                <th>Хосты</th>
                                <th>Куки</th>
                            </tr>
                        </thead>
                    @forelse ($referers as $name => $val)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $val['visits'] }}</td>
                        <td>{{ $val['hosts'] }}</td>
                        <td>{{ $val['cookies'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">Нет данных</td>
                    </tr>
                    @endforelse
                    </table>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

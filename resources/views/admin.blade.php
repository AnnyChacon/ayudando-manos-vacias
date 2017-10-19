@extends('layouts.master')

@section('contenido')<div class="cabecera-titulo">
    <div class="container">
        <div class="row">
            <h2>Bienvenido Administrador</h2>
        </div>
    </div>
</div>
<div class="container" id="cuerpo">
    <div class="row">
        <div class="col-sm-12 col-md-10 col-md-offset-1">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#perfil">Perfil</a></li>
                <li><a data-toggle="tab" href="#menu1">Tipo de Cáncer</a></li>
                <li><a data-toggle="tab" href="#menu2">Medicamento</a></li>
                <li><a data-toggle="tab" href="#menu3">Categoria de Insumo</a></li>
                <li><a data-toggle="tab" href="#menu4">Localidad</a></li>
                <li><a data-toggle="tab" href="#menu5">Administrador</a></li>
            </ul>

            <div class="tab-content">
                <div id="perfil" class="tab-pane fade in active">
                    <form action="{{ route('actualizar-perfil') }}" method="post" class="row">
                        <h3>Perfil</h3>
                        <div class="row">
                            <div class="col-xs-3">
                                <label for="nombre">Nombre completo</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user-circle"></i></span>
                                    <input name="nombre" type="text" class="form-control" value="{{ $nombre }}">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <label for="usuario">Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user-circle"></i></span>
                                    <input name="usuario" type="text" disabled class="form-control" value="{{ Auth::user()->usuario }}">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label for="correo">Correo</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope-open-o"></i></span>
                                    <input name="correo" type="email" class="form-control" value="{{ Auth::user()->correo }}">
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-xs-4">
                                <label for="contrasena1">Contraseña nueva</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input name="contrasena1" type="password" class="form-control" placeholder="Ingrese contraseña">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <label for="contrasena2">Confirmar contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-repeat"></i></span>
                                    <input name="contrasena2" type="password" class="form-control" placeholder="Confirmar contraseña">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <button type="submit" class="btn btn-deafult btn-block btn-md button-reg abajo">Actualizar</button>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                    </form>
                </div>
                <div id="menu1" class="tab-pane fade">
                    <form action="{{ route('guardar-tipo-cancer') }}" method="post" class="row">
                        <h3>Agregar tipo de cáncer</h3>
                        <div class="col-xs-4">
                            <label for="tipo">Tipo</label>
                            <input name="tipo" type="text" class="form-control" required placeholder="Ingrese tipo de cáncer">
                        </div>
                        <div class="col-xs-4">
                            <label for="desc">Descripción</label>
                            <input name="desc" type="text" class="form-control" required placeholder="Ingrese una descripción">
                        </div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-deafult btn-block btn-md button-reg abajo">Agregar</button>
                        </div>
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                    </form>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <form action="{{ route('guardar-medicamento') }}" method="post" class="row">
                        <h3>Agregar medicamento</h3>
                        <div class="col-xs-4">
                            <label for="nombre">Nombre</label>
                            <input name="nombre" type="text" class="form-control" required placeholder="Ingrese nombre del medicamento">
                        </div>
                        <div class="col-xs-4">
                            <label for="desc">Descripción</label>
                            <input name="desc" type="text" class="form-control" required placeholder="Ingrese una descripción">
                        </div>
                        <div class="col-xs-4">
                            <button type="submit" class="btn btn-deafult btn-block btn-md button-reg abajo">Agregar</button>
                        </div>
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                    </form>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <form action="{{ route('guardar-categoria-insumo') }}" method="post" class="row">
                        <h3>Agregar categoria de insumo</h3>
                        <div class="col-xs-3">
                            <label for="cat">Categoria</label>
                            <input name="categoria" type="text" class="form-control" required placeholder="Ingrese categoria">
                        </div>
                        <div class="col-xs-3">
                            <button type="submit" class="btn btn-deafult btn-block btn-md button-reg abajo">Agregar</button>
                        </div>
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                    </form>
                </div>
                <div id="menu4" class="tab-pane fade">
                    <form action="{{ route('guardar-localidad') }}" method="post" class="row">
                        <h3>Agregar localidad</h3>
                        <div class="col-xs-3">
                            <label for="nombre">Nombre</label>
                            <input name="nombre" type="text" class="form-control" required placeholder="Ingrese nombre">
                        </div>
                        <div class="col-xs-3">
                            <label for="tipo">Tipo</label><br>
                            <label class="radio-inline">
                                <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="1" checked="checked"> Ciudad
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="2"> Estado
                            </label>
                        </div>
                        <div class="col-xs-3">
                            <label for="estado">Estado</label>
                            <select name="estado" class="form-control" id="estado">
                                <option value="0" selected>Selecciona un Estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-3">
                            <button type="submit" class="btn btn-deafult btn-block btn-md button-reg abajo">Agregar</button>
                        </div>
                        <input type="hidden" id="localidad_id" name="localidad_id">
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                    </form>
                </div>
                <div id="menu5" class="tab-pane fade">
                    <form action="{{ route('guardar-admin') }}" method="post" class="row">
                        <h3>Agregar Administrador</h3>
                        <div class="row">
                            <div class="col-xs-3">
                                <label for="nombre">Nombre completo</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user-circle"></i></span>
                                    <input name="nombre" type="text" id="nombre" class="form-control" placeholder="Ingrese nombre y apellido">
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <label for="usuario">Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-user-circle"></i></span>
                                    <input name="usuario" type="text" id="usuario" class="form-control" placeholder="Ingrese Usuario">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <label for="correo">Correo</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-envelope-open-o"></i></span>
                                    <input name="correo" type="email" id="correo" class="form-control" placeholder="Ingrese correo">
                                </div>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-xs-4">
                                <label for="contrasena1">Contraseña nueva</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input name="contrasena1" type="password" id="contrasena1" class="form-control" placeholder="Ingrese contraseña">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <label for="contrasena2">Confirmar contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-repeat"></i></span>
                                    <input name="contrasena2" type="password" id="contrasena2" class="form-control" placeholder="Confirmar contraseña">
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <button type="submit" class="btn btn-deafult btn-block btn-md button-reg abajo">Guardar</button>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="{{ Session::token() }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
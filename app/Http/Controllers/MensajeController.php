<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mensaje;
use App\Nino;
use Carbon\Carbon;

class MensajeController extends Controller
{
    public function guardarMensaje(Request $request)
    {
        $nino = Nino::find($request['nino_id']);
        if(!$nino)
            return redirect()->route('inicio');

        $mensaje = new Mensaje;
        $mensaje->mensaje = $request['mensaje'];
        $mensaje->correo_remitente = $request['correo'];
        $mensaje->nombre_apellido_remitente = $request['nombre'];
        $mensaje->fecha = Carbon::now('America/Caracas');
        if($request->has('telefono')){
            $this->validate($request,[
                'telefono' => 'size:11'
            ]);
        }
            $mensaje->telefono_remitente = $request['telefono'];
        $mensaje->nino_id = $request['nino_id'];

        $mensaje->save();

        return redirect()->back();
    }
}
